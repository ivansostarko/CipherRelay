<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use IvanSostarko\OttoCrypt\Facades\OttoCrypt as Otto;

class SubmissionController extends Controller
{
    public function create()
    {
        return view('user.first');
    }

    protected function generatePasscode(): string
    {
        // 4 random words from seed list (see seeder), fallback small list
        $words = config('pass_words', ['alpha','bravo','charlie','delta','echo','foxtrot','golf','hotel','india','juliet','kilo','lima','mike','november','oscar','papa','quebec','romeo','sierra','tango','uniform','victor','whiskey','xray','yankee','zulu']);
        $parts = [];
        for ($i=0; $i<4; $i++) {
            $parts[] = $words[random_int(0, count($words)-1)];
        }
        return implode('-', $parts);
    }

    protected function deriveThreadKey(string $passcode): string
    {
        // 32-byte raw key derived from passcode (deterministic)
        return hash('sha256', $passcode, true);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:5000',
            'file' => 'nullable|file|max:51200', // 50MB demo
        ]);

        $passcode = $this->generatePasscode();
        $threadKey = $this->deriveThreadKey($passcode);

        // Encrypt and store thread key with master password so admin can decrypt without passcode
        [$keyCipher, $keyHeader] = Otto::encryptString(base64_encode($threadKey), options: ['password' => env('OTTO_MASTER_PASSWORD')]);

        $thread = Thread::create([
            'passcode_hash' => Hash::make($passcode),
            'status' => 'new_from_user',
            'key_cipher' => base64_encode($keyCipher),
            'key_header' => base64_encode($keyHeader),
        ]);

        // Encrypt subject & body using raw thread key
        [$subCipher, $subHeader] = Otto::encryptString($data['subject'], options: ['raw_key' => $threadKey]);
        [$bodyCipher, $bodyHeader] = Otto::encryptString($data['message'], options: ['raw_key' => $threadKey]);

        $filePath = null; $original = null; $size = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $original = $file->getClientOriginalName();
            $size = $file->getSize();
            // Store encrypted file to storage/private/messages
            $inPath = $file->storeAs('private/tmp', (string) Str::uuid(), 'local');
            $out = 'private/messages/'.(string) Str::uuid().'.otto';
            $inAbs = Storage::disk('local')->path($inPath);
            $outAbs = Storage::disk('local')->path($out);
            Otto::encryptFile($inAbs, $outAbs, options: ['raw_key' => $threadKey]);
            @unlink($inAbs);
            $filePath = $out;
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

        Cache::forget("thread:{$thread->id}:messages");
        Cache::forget("admin:threads:index:*");

        // Store session for immediate access
        session()->put('thread_id', $thread->id);
        session()->put('thread_key', base64_encode($threadKey));

        return view('user.passcode', ['passcode' => $passcode]);
    }
}
