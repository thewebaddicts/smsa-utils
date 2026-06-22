<?php

namespace twa\smsautils\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use twa\apiutils\Traits\APITrait;
use twa\smsautils\Http\Controllers\Controller;

class ForceUpdateController extends Controller
{

    use APITrait;

    public function release(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'app' => 'file',
            'manifest' => 'required|file',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        $manifest = json_decode(file_get_contents($request->file('manifest')), true);

        if (!isset($manifest['version']) && !isset($manifest['platform'])) {
            return $this->response(notification()->error('Error', 'Invalid manifest file.'));
        }

        $version = $manifest['version'];
        $platform = $manifest['platform'];

        $file_name = 'v' . str_replace('.', '_', $version) . '.apk';

        if (request()->has('app')) {
            Storage::disk('public')
                ->putFileAs(  $platform, $request->file('app'), $file_name);
        }

        Storage::disk('public')
            ->putFileAs( $platform, $request->file('manifest'), 'manifest.json');


        return $this->responseData([
            'message' => 'New version uploaded successfully',
        ]);
    }


    public function check(Request $request)
    {
        $request->validate([
            'version' => 'required|string',
            'platform' => 'required|string|in:android,ios',
        ]);

        $platform = request()->input('platform');

        $file_content = Storage::disk('public')->get("$platform/manifest.json");

        if(!$file_content) {
            return $this->responseData([
                'force_update' => true,
                'url' => null,
            ]);
        }

        $manifest = json_decode($file_content, 1);

        if(!is_array($manifest)) {
            return $this->responseData([
                'force_update' => true,
                'url' => null,
            ]);
        }
      

        $file_name = 'v' . str_replace('.', '_', $manifest["version"]) . '.apk';


        if ($manifest["version"] > $request->version) {
            return $this->responseData([
                'force_update' => true,
                'url' => $platform == 'android' ? Storage::disk('public')->url("$platform/$file_name") : ($manifest['link'] ?? null),
            ]);
        }

        return $this->responseData([
            'force_update' => false,
            'url' => null,
        ]);
    }

    // public function link(Request $request, $platform, $version)
    // {

    //     $file_name = 'v' . str_replace('.', '_', $version) . '.apk';

    //     switch ($platform) {
    //         case 'android':
    //             return Storage::disk('public')->url('app/public/android/' . $file_name);

    //         case 'ios':
    //             return null;
    //     }

    //     return null;
    // }
}
