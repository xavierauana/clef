<?php namespace Xavierau\Clef\Http\Controllers;

use Xavierau\Clef\Clef;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ClefController extends Controller {
    /**
     * @var \Xavierau\Cleflogin\Clef
     */
    private $clef;

    function __construct(Clef $clef)
    {
        $this->clef = $clef;
    }


    public function login(Request $request)
    {
        $this->clef->login($request->get('state'), $request->get('code'));
        $this->clef->fetchUserInfo();
        $userInfo = $this->clef->getUserInfo();
        $this->resetSession($userInfo);

        return redirect('hello');

    }

    private function resetSession($userInfo)
    {
        session()->flush();
        foreach($userInfo as $key => $val)
        {
            session([$key=>$val]);
        }
    }


}
