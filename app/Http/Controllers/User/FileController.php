<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use IvanSostarko\OttoCrypt\Facades\OttoCrypt as Otto;

class FileController extends Controller
{
    public function download(Request $request, Message $message)
    {
        $threadId = $request->session()->get('thread_id');
        $threadKey = base64_decode($request->session()->get('thread_key'));
        if ($message->thread_id != $threadId || !$message->file_path) {
            abort(403);
        }

        $in = storage_path('app/'.$message->file_path);
        $tmp = storage_path('app/private/tmp/'.uniqid('dec_', true));
        @mkdir(dirname($tmp), 0775, true);
        Otto::decryptFile($in, $tmp, options: ['raw_key' => $threadKey]);
        $content = file_get_contents($tmp);
        @unlink($tmp);
        return Response::make($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.basename($message->file_original ?? 'file').'"'
        ]);
    }
}
