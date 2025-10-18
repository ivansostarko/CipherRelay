<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Thread;
use App\Models\Message;
use App\Models\MessageNote;
use Illuminate\Support\Facades\Hash;
use IvanSostarko\OttoCrypt\Facades\OttoCrypt as Otto;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $passcode = 'alpha-bravo-charlie-delta';
        $threadKey = hash('sha256', $passcode, true);

        // store encrypted copy of thread key with master password (fallback to a default for dev)
        $master = env('OTTO_MASTER_PASSWORD', 'dev-master-password');
        [$kc, $kh] = Otto::encryptString(base64_encode($threadKey), options: ['password' => $master]);

        $thread = Thread::create([
            'passcode_hash' => Hash::make($passcode),
            'status' => 'new_from_user',
            'key_cipher' => base64_encode($kc),
            'key_header' => base64_encode($kh),
        ]);

        [$sc, $sh] = Otto::encryptString('Hello', options: ['raw_key' => $threadKey]);
        [$bc, $bh] = Otto::encryptString('This is a demo seeded message from the user.', options: ['raw_key' => $threadKey]);

        Message::create([
            'thread_id' => $thread->id,
            'direction' => 'user',
            'subject_cipher' => base64_encode($sc),
            'subject_header' => base64_encode($sh),
            'body_cipher' => base64_encode($bc),
            'body_header' => base64_encode($bh),
        ]);

        MessageNote::create([
            'thread_id' => $thread->id,
            'note' => 'Initial triage note.',
        ]);
    }
}
