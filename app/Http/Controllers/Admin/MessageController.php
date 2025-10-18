<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageNote;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use IvanSostarko\OttoCrypt\Facades\OttoCrypt as Otto;

class MessageController extends Controller
{
    protected function getThreadKey(Thread $thread): string
    {
        // Decrypt stored key using master password
        $key = Otto::decryptString(
            base64_decode($thread->key_cipher),
            base64_decode($thread->key_header),
            options: ['password' => env('OTTO_MASTER_PASSWORD')]
        );
        return base64_decode($key);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $page = (int) $request->query('page', 1);

        $cacheKey = "admin:threads:index:q={$search}:status={$status}:page={$page}";
        $data = Cache::remember($cacheKey, 30, function () use ($search, $status) {
            $query = Thread::query()->orderByDesc('updated_at');
            if ($status) {
                $query->where('status', $status);
            }
            // We cannot search encrypted subject at DB level, so full scan decrypt and filter
            $threads = $query->with('messages')->paginate(10);
            // Attach first message subject decrypted for list
            $threads->getCollection()->transform(function($t) {
                $first = $t->messages->first();
                if ($first) {
                    try {
                        $key = $this->getThreadKey($t);
                        $subject = Otto::decryptString(base64_decode($first->subject_cipher), base64_decode($first->subject_header), options: ['raw_key' => $key]);
                        $t->first_subject = $subject;
                    } catch (\Throwable $e) {
                        $t->first_subject = '(decrypt error)';
                    }
                } else {
                    $t->first_subject = '(no messages)';
                }
                return $t;
            });
            return $threads;
        });

        // post-filter by search in memory
        if ($search) {
            $data->setCollection($data->getCollection()->filter(function($t) use ($search) {
                return stripos((string)$t->first_subject, $search) !== false;
            })->values());
        }

        return view('admin.messages.index', ['threads' => $data, 'q' => $search, 'status' => $status]);
    }

    public function show(Thread $thread)
    {
        $key = $this->getThreadKey($thread);
        $messages = Cache::remember("thread:{$thread->id}:messages", 60, function () use ($thread) {
            return $thread->messages()->get();
        });
        $viewMsgs = $messages->map(function($m) use ($key) {
            $subject = Otto::decryptString(base64_decode($m->subject_cipher), base64_decode($m->subject_header), options: ['raw_key' => $key]);
            $body = Otto::decryptString(base64_decode($m->body_cipher), base64_decode($m->body_header), options: ['raw_key' => $key]);
            return (object) [
                'id' => $m->id,
                'direction' => $m->direction,
                'subject' => $subject,
                'body' => nl2br(e($body)),
                'file_path' => $m->file_path,
                'file_original' => $m->file_original,
                'file_size' => $m->file_size,
                'created_at' => $m->created_at,
            ];
        });

        $notes = $thread->notes()->get();

        return view('admin.messages.show', compact('thread','viewMsgs','notes'));
    }

    public function addMessage(Request $request, Thread $thread)
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:5000',
            'file' => 'nullable|file|max:51200',
        ]);

        $key = $this->getThreadKey($thread);

        [$subCipher, $subHeader] = Otto::encryptString($request->subject, options: ['raw_key' => $key]);
        [$bodyCipher, $bodyHeader] = Otto::encryptString($request->message, options: ['raw_key' => $key]);

        $filePath=null;$original=null;$size=null;
        if ($request->hasFile('file')) {
            $file=$request->file('file');
            $original=$file->getClientOriginalName();
            $size=$file->getSize();
            $in=$file->storeAs('private/tmp',(string) \Illuminate\Support\Str::uuid(),'local');
            $out='private/messages/'.(string) \Illuminate\Support\Str::uuid().'.otto';
            Otto::encryptFile(storage_path('app/'.$in), storage_path('app/'.$out), options: ['raw_key' => $key]);
            @unlink(storage_path('app/'.$in));
            $filePath=$out;
        }

        Message::create([
            'thread_id' => $thread->id,
            'direction' => 'admin',
            'subject_cipher' => base64_encode($subCipher),
            'subject_header' => base64_encode($subHeader),
            'body_cipher' => base64_encode($bodyCipher),
            'body_header' => base64_encode($bodyHeader),
            'file_path' => $filePath,
            'file_original' => $original,
            'file_size' => $size,
        ]);

        $thread->update(['status' => 'add_more']);

        Cache::forget("thread:{$thread->id}:messages");
        Cache::forget("admin:threads:index:*");

        return redirect()->route('admin.messages.show', $thread)->with('ok','Message sent.');
    }

    public function updateStatus(Request $request, Thread $thread)
    {
        $request->validate(['status' => 'required|in:new_from_user,add_more,closed']);
        $thread->update(['status' => $request->status]);
        Cache::forget("admin:threads:index:*");
        return back()->with('ok','Status updated.');
    }

    public function addNote(Request $request, Thread $thread)
    {
        $request->validate(['note'=>'required|string|max:1000']);
        MessageNote::create(['thread_id'=>$thread->id,'note'=>$request->note]);
        return back()->with('ok','Note added.');
    }

    public function download(Thread $thread, Message $message)
    {
        if ($message->thread_id !== $thread->id || !$message->file_path) abort(404);
        $key = $this->getThreadKey($thread);
        $in = storage_path('app/'.$message->file_path);
        $tmp = storage_path('app/private/tmp/'.uniqid('dec_', true));
        @mkdir(dirname($tmp), 0775, true);
        Otto::decryptFile($in, $tmp, options: ['raw_key' => $key]);
        $content = file_get_contents($tmp);
        @unlink($tmp);
        return Response::make($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.basename($message->file_original ?? 'file').'"'
        ]);
    }
}
