<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use IvanSostarko\OttoCrypt\Facades\OttoCrypt as Otto;

class ThreadController extends Controller
{
    public function enterPasscode()
    {
        return view('user.enter-passcode');
    }

    protected function deriveThreadKey(string $passcode): string
    {
        return hash('sha256', $passcode, true);
    }

    public function authenticate(Request $request)
    {
        $request->validate(['passcode' => 'required|string']);
        $passcode = strtolower(trim($request->passcode));

        // Find candidate threads (simple scan, then verify hash) - in real systems you'd index differently
        $thread = Thread::orderByDesc('id')->get()->first(function($t) use ($passcode) {
            return password_verify($passcode, $t->passcode_hash);
        });

        if (!$thread) {
            return back()->withErrors(['passcode' => 'Invalid passcode.']);
        }

        session()->put('thread_id', $thread->id);
        session()->put('thread_key', base64_encode($this->deriveThreadKey($passcode)));

        return redirect()->route('thread.show');
    }

    public function show(Request $request)
    {
        $threadId = $request->session()->get('thread_id');
        $threadKey = base64_decode($request->session()->get('thread_key'));
        $thread = Thread::findOrFail($threadId);

        $messages = Cache::remember("thread:{$threadId}:messages", 60, function () use ($thread) {
            return $thread->messages()->get();
        });

        // Decrypt for display
        $viewMsgs = $messages->map(function($m) use ($threadKey) {
            $subject = Otto::decryptString(base64_decode($m->subject_cipher), base64_decode($m->subject_header), options: ['raw_key' => $threadKey]);
            $body = Otto::decryptString(base64_decode($m->body_cipher), base64_decode($m->body_header), options: ['raw_key' => $threadKey]);
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

        return view('user.thread', [
            'thread' => $thread,
            'messages' => $viewMsgs,
        ]);
    }

    public function addMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:5000',
            'file' => 'nullable|file|max:51200',
        ]);
        $threadId = $request->session()->get('thread_id');
        $threadKey = base64_decode($request->session()->get('thread_key'));
        $thread = Thread::findOrFail($threadId);

        [$subCipher, $subHeader] = Otto::encryptString($request->subject, options: ['raw_key' => $threadKey]);
        [$bodyCipher, $bodyHeader] = Otto::encryptString($request->message, options: ['raw_key' => $threadKey]);

        $filePath=null;$original=null;$size=null;
        if ($request->hasFile('file')) {
            $file=$request->file('file');
            $original=$file->getClientOriginalName();
            $size=$file->getSize();
            $in=$file->storeAs('private/tmp',(string) Str::uuid(),'local');
            $out='private/messages/'.(string) Str::uuid().'.otto';
            Otto::encryptFile(storage_path('app/'.$in), storage_path('app/'.$out), options: ['raw_key' => $threadKey]);
            @unlink(storage_path('app/'.$in));
            $filePath=$out;
        }

        Message::create([
            'thread_id' => $thread->id,
            'direction' => 'user',
            'subject_cipher' => base64_encode($subCipher),
            'subject_header' => base64_encode($subHeader),
            'body_cipher' => base64_encode($bodyCipher),
            'body_header' => base64_encode($bodyHeader),
            'file_path' => $filePath,
            'file_original' => $original,
            'file_size' => $size,
        ]);

        $thread->update(['status' => 'new_from_user']);

        Cache::forget("thread:{$thread->id}:messages");
        Cache::forget("admin:threads:index:*");

        return redirect()->route('thread.show')->with('ok','Message added.');
    }

    public function deleteMessage(Request $request, Message $message)
    {
        $threadId = $request->session()->get('thread_id');
        if ($message->thread_id != $threadId || $message->direction !== 'user') {
            abort(403);
        }
        if ($message->file_path) {
            @unlink(storage_path('app/'.$message->file_path));
        }
        $message->delete();
        Cache::forget("thread:{$threadId}:messages");
        Cache::forget("admin:threads:index:*");
        return back()->with('ok','Message deleted.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['thread_id','thread_key']);
        return redirect()->route('landing');
    }
}
