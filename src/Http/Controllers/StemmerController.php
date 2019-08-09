<?php


namespace Nouralhadi\Stemmer\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nouralhadi\Stemmer\Http\Helpers\ISRIStemmer;

class StemmerController extends Controller {

    private $Stemmer;

    public function __construct(ISRIStemmer $stemmer){
        $this->Stemmer = $stemmer;
    }

    public function index(){
        return view('stemmer::index');
    }

    public function stem(Request $request){
        $query = $request->get('string');
        $words = mb_split(' ',$query);

        $ret = [];
        foreach ($words as $word){
            array_push($ret, $this->Stemmer->stem($word));
        }
        $result = implode(' ', $ret);

        return view('stemmer::result',[
            'result' => $result
        ]);
    }
}
