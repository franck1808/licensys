<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    //
    public function index(){

        return view('app.dashboard');

    }

    public function allCustomers(){

        $customers = DB::table('customers')->get();

        $data = [
            'customers'=>$customers,
        ];

        return view('app.customers.listcustomers', $data);

    }

    public function createCustomer(Request $request){

        try {
            DB::beginTransaction();

            DB::table('customers')->insert([
                'name'=>$request->name,
                'email'=>$request->email,
                'isActive'=>$request->isActive,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);

            DB::commit();

            return back()->with('success', 'The customer has been created');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('fail', 'An error has occurred while processing your request. Message: '.$e->getMessage());
        }

    }

    public function generateAPIKey($customer_id){

        try {

            $customer_info = DB::table('customers')->where('id', $customer_id)->first();
        
            $randomString = Str::upper(Str::random(20, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'));

            DB::beginTransaction();

            DB::table('customers')->where('id', $customer_id)->update([
                'api_key'=>$randomString,
                'updated_at'=>now(),
            ]);

            DB::commit();

            return back()->with('success', 'API Key has been generated for '.$customer_info->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('fail', 'An error has occurred while processing your request. Message: '.$e->getMessage());
        }

    }

    public function allCustomersApps(){

        $customers = DB::table('customers')->where('isActive', 1)->get();

        $customer_apps = DB::table('customer_apps')
        ->join('customers', 'customer_apps.customer_id', 'customers.id')
        ->select('customer_apps.*', 'customers.name as customer_name')
        ->get();

        $data = [
            'customers'=>$customers,
            'customer_apps'=>$customer_apps,
        ];

        return view('app.customers.apps.listcustomer_apps', $data);

    }

    public function createCustomerApp(Request $request){

        try {
            DB::beginTransaction();

            $randomString = Str::upper(Str::random(10, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'));

            $code = 'APP_'.$randomString;

            DB::table('customer_apps')->insert([
                'name'=>$request->name,
                'customer_id'=>$request->customer_id,
                'code_app'=>$code,
                'status'=>$request->status,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);

            DB::commit();

            return back()->with('success', 'The APP '.$request->name.' - '.$code.' has been created.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('fail', 'An error has occurred while processing your request. Message: '.$e->getMessage());
        }

    }

    private function insertDashesRandomly($string, $numDashes = 4) {
        // Convertir la chaîne en tableau pour faciliter l'insertion
        $array = str_split($string);
        $length = count($array);
    
        // S'assurer que le nombre de tirets ne dépasse pas la longueur de la chaîne moins 2 (début et fin exclus)
        $numDashes = min($numDashes, $length - 2);
    
        // Conserver les positions aléatoires choisies pour éviter les duplications
        $positions = [];
    
        // Générer des positions aléatoires sans inclure le début (index 0) et la fin (index $length - 1)
        while (count($positions) < $numDashes) {
            $position = rand(1, $length - 2); // Position aléatoire excluant début (0) et fin ($length - 1)
            if (!in_array($position, $positions)) {
                $positions[] = $position;
            }
        }
    
        // Insérer les tirets aux positions aléatoires choisies
        foreach ($positions as $position) {
            array_splice($array, $position, 0, '-'); // Insérer le tiret à la position spécifiée
        }
    
        // Reconvertir le tableau en chaîne de caractères
        return implode('', $array);
    }

    public function allLicenseKeys(){

        $licenses = DB::table('licensekeys')
        ->join('customer_apps', 'licensekeys.customer_app_id', 'customer_apps.id')
        ->join('customers', 'customer_apps.customer_id', 'customers.id')
        ->select('licensekeys.*', 'customers.name as customer_name', 'customer_apps.name as customer_app_name')
        ->get();

        $customer_apps = DB::table('customer_apps')->where('customer_apps.status', 1)
        ->join('customers', 'customer_apps.customer_id', 'customers.id')
        ->select('customer_apps.*', 'customers.name as customer_name')
        ->get();

        

        $data = [
            'licenses'=>$licenses,
            'customer_apps'=>$customer_apps,
        ];

        return view('app.customers.apps.list_license', $data);

    }

    public function createLicenseKey(Request $request){

        try {

            $get_license_info = DB::table('licensekeys')->where('customer_app_id', $request->customer_app_id)->whereIn('isActive', [0, 1, 2])->first();

            $get_app_info = DB::table('customer_apps')->where('customer_apps.id', $request->customer_app_id)
            ->join('customers', 'customer_apps.customer_id', 'customers.id')
            ->select('customer_apps.*', 'customers.api_key as api_key', 'customers.name as customer_name')
            ->first();

            if ($get_license_info) {
                return back()->with('fail', 'An error has occurred while processing your request. Message: A valid license key for '.$get_app_info->name.' - '.$get_app_info->customer_name.' already exists.');
            } else {
                DB::beginTransaction();

                $randomString = Str::upper(Str::random(10, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'));
                
                $short_code_app = Str::substr($get_app_info->code_app, 4, 10);

                $key = $short_code_app.''.$get_app_info->api_key.''.$randomString;

                $key = 'KEY_'.$this->insertDashesRandomly($key);

                DB::table('licensekeys')->insert([
                    'key'=>$key,
                    'customer_app_id'=>$request->customer_app_id,
                    'isActive'=>$request->isActive,
                    'renew'=>0,
                    'start_at'=>$request->start_at,
                    'end_at'=>$request->end_at,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);

                DB::commit();

                return back()->with('success', 'The license key for '.$get_app_info->name.' - '.$get_app_info->customer_name.' has been created.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('fail', 'An error has occurred while processing your request. Message: '.$e->getMessage());
        }

    }

    public function checkKey(Request $request){

        try {
            $check_key = DB::table('licensekeys')->where('key', $request->key)->exists();

            if ($check_key) {
                $key_info = DB::table('licensekeys')->where('key', $request->key)->first();

                if ($key_info->isActive != 1) {
                    return response()->json([
                        'status'=>404,
                        'message'=>'The license key has expired or suspended.'
                    ]);
                } else {
                    $key = Str::substr($key_info->key, 4, 44);

                    $key = implode('', explode('-', $key));

                    $customer_key = Str::substr($key, 10, 20);

                    $app_key = 'APP_'.Str::substr($key, 0, 10);

                    $customer_info = DB::table('customers')->where('api_key', $customer_key)->first();

                    if ($customer_info->isActive != 1) {
                        return response()->json([
                            'status'=>404,
                            'message'=>'Your customer api key has inactive or suspended.'
                        ]);
                    } else {
                        $app_info = DB::table('customer_apps')->where('code_app', $app_key)->first();

                        if ($app_info->status != 1) {
                            return response()->json([
                                'status'=>404,
                                'message'=>'This product code has inactive or suspended.'
                            ]);
                        } else {
                            $end = Carbon::parse($key_info->end_at);
                            if (date('Y-m-d') <= $end) {
                                $remain = $end->diffInDays(date('Y-m-d'));
                            } else {
                                $remain = 0;
                            }

                            return response()->json([
                                'status'=>200,
                                'message'=>'Your license key is still valid for '.$remain.' days.'
                            ]);
                        }
                        
                    }
                }

            } else {
                return response()->json([
                    'status'=>404,
                    'message'=>'The license key is not found.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message'=>$e->getMessage()
            ]);
        }

    }

    public function getInfoKey(Request $request){

        try {
            $check_key = DB::table('licensekeys')->where('key', $request->key)->exists();

            if ($check_key) {
                $key_info = DB::table('licensekeys')->where('key', $request->key)
                ->select('start_at', 'end_at', 'isActive')
                ->first();

                if ($key_info->isActive != 1) {
                    return response()->json([
                        'start_at'=>$key_info->start_at,
                        'end_at'=>$key_info->end_at,
                        'isActive'=>"Your license key has expired or suspended.",
                        'status'=>404,
                        'remaining_time'=>0
                    ]);
                } else {
                    $end = Carbon::parse($key_info->end_at);
                    if (date('Y-m-d') <= $end) {
                        $remain = $end->diffInDays(date('Y-m-d'));
                    } else {
                        $remain = 0;
                    }
                    return response()->json([
                        'start_at'=>$key_info->start_at,
                        'end_at'=>$key_info->end_at,
                        'isActive'=>"Your license key is still valid.",
                        'status'=>200,
                        'remaining_time'=>$remain
                    ]);
                }

            } else {
                return response()->json([
                    'start_at'=>NULL,
                    'end_at'=>NULL,
                    'isActive'=>"Your license key is not found.",
                    'status'=>404,
                    'remaining_time'=>0
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message'=>$e->getMessage()
            ]);
        }

    }


}
