<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/14/2015
 * Time: 4:09 PM
 */

namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use App\Model\Payout;
use Illuminate\Http\Request;
use DB;
use Log;

class CashOutController extends Controller
{
    public function payout($payoutId)
    {
        $payout = DB::select('SELECT id, amount, phone FROM sp_payout WHERE id = :id', ['id' => $payoutId]);
        if($payout[0]->phone == 'ERROR'){
            return response()->json(['error'=>1, 'message'=>env(2042)]);
        }
        $curl = curl_init();
        $data = array('amount'=>$payout[0]->amount/100, 'invoice_id'=>'sp'.$payout[0]->id, 'recipient'=>$payout[0]->phone);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sandbox.xfers.io/api/v3/payouts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CAINFO, env('CACERT_DIR'),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "content-type: multipart/form-data; boundary=---011000010111000001101001",
                "x-xfers-user-api-key: ".env('X-XFERS-USER-API-KEY')
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
             return response()->json(['error'=>1, 'message'=>$err]);
        } else {
            $rs = json_decode($response);
            if(!empty($rs->error)){
                return response()->json(['error'=>1, 'message'=>$rs->error]);
            }

            DB::select('INSERT INTO sp_payouthistory (payoutId, xferId, dateCreated, status, action, request, response) VALUES (:payoutId, :xferId, :date, :status, :action, :request, :response)',[
                'payoutId'=>$payoutId,
                'xferId'=>$rs->id,
                'date'=>date('Y-m-d H:i:s'),
                'status'=>$rs->status,
                'action'=>'Creating a Payout',
                'request'=>'https://sandbox.xfers.io/api/v3/payouts',
                'response'=>$response
                ]);
            DB::select('UPDATE sp_payout set status = :status, xferId = :xferId where id = :payoutId', ['status'=>$rs->status, 'xferId'=>$rs->id, 'payoutId'=>$payoutId]);
            return response()->json(['error'=>0, 'message'=>$rs->status]);
        }
    }

    public function updatePayoutStatusFromXfers(){

        $rs = DB::select('SELECT id, xferId from sp_payout where status = \'unclaimed\'');
        $changed = array();
        if(sizeof($rs) > 0){
            foreach($rs as $r){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://sandbox.xfers.io/api/v3/payouts/".$r->xferId,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_CAINFO, env('CACERT_DIR'),
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "content-type: multipart/form-data; boundary=---011000010111000001101001",
                        "x-xfers-user-api-key: ".env('X-XFERS-USER-API-KEY')
                    ),
                ));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    Log::info('Error request to xfers: '.$err);
                } else {
                    $res = json_decode($response);
                    if($res->status != 'unclaimed'){
                        DB::select('INSERT INTO sp_payouthistory (payoutId, xferId, dateCreated, status, action, request, response) VALUES (:payoutId, :xferId, :date, :status, :action, :request, :response)',[
                            'payoutId'=>$r->id,
                            'xferId'=>$r->xferId,
                            'date'=>date('Y-m-d H:i:s'),
                            'status'=>$res->status,
                            'action'=>'Retrieve a Payout',
                            'request'=>'https://sandbox.xfers.io/api/v3/payouts/'.$r->xferId,
                            'response'=>$response
                        ]);
                        DB::select('UPDATE sp_payout set status = :status where id = :payoutId', ['status'=>$res->status, 'payoutId'=>$r->id]);
                        if($res->status == 'cancelled') $this->cancelPayment($r->id);
                        array_push($changed, ['id'=>$r->id, 'status'=>$r->status]);
                    }
                }
            }
        }
        return response()->json($changed);
    }

    public function cashout(Request $request){
        $coins = $request->input('amount');
        if($coins < 100){
            return response()->json(['status'=>0, 'error'=>['code'=>2041, 'message'=>env(2041)]]);
        }
        // calculator coins to SGD (100 coins = 1SGD$)
        //$ext = $amount%100;
        //$sgd = ($coins/100); //payout fee will be calculator in produce createPayout
        /*if($ext > 0){ //you need to transfer multiple of 100
            return response()->json(['status'=>0, 'error'=>['code'=>2043,'message'=>env(2043)]]);
        }*/
        $payout = DB::select('CALL createPayout(:user, :coins, :amount, :date)', ['user'=>$request->attributes->get('userId'), 'coins'=>$coins, 'amount'=>$coins, 'date'=>date('Y-m-d H:i:s')]);
        if($payout[0]->id == 0){
            return response()->json(['status'=>0, 'error'=>['code'=>2040,'message'=>env(2040)]]);
        }
        return response()->json(['status'=>1]);
    }

    public function managePayout(){
        $page_title = "Payout Management";
        $allData = Payout::all();
        return view('backend/payout', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function manualPayment($id){
        $currentDate = date("Y-m-d H:i:s");
        try {
            $payout = Payout::find($id);
            $payout->status = "SUCCESS";
            $payout->datePayout = $currentDate;
            $payout->dateSuccess = $currentDate;
            $payout->save();
            /*
            DB::select('INSERT INTO sp_mail_box (kind, title, content, userId, createdBy, dateCreated) VALUES (:kind, :title, :content, :userId, :createdBy, :dateCreated)',
                ['kind'=>1, 'title'=>'Payout', 'content'=>'Your cash-out transaction has been processed! $'.$payout->amount.' has been banked in your account.', 'userId'=>$payout->userId, 'createdBy'=>'System', 'dateCreated'=>date('Y-m-d H:i:s')]);
            */
            echo json_encode(array("error" => 0));
        } catch (\PDOException $e) {
            echo json_encode(array("error" => 1, "message" => $e->getMessage()));
        }
    }

    public function cancelPayment($id){
        try {
            $payout = Payout::find($id);
            $payout->status = "CANCEL";
            $payout->save();
            DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                'user' => $payout->userId,
                'exp' => 0,
                'token' => 0,
                'gold' => $payout->amount,
                'reason' => 'Cancel payment, return back coins',
                'date' => date("Y-m-d H:i:s")
            ]);
            echo json_encode(array("error" => 0));
        } catch (\PDOException $e) {
            echo json_encode(array("error" => 1, "message" => $e->getMessage()));
        }
    }
}