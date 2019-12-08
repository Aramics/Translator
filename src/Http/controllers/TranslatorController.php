<?php

namespace Aramics\Translator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App;
use App\Http\Requests;
use Excel;
use Input;
use File;
use Exception;
use Auth;
use Lang;
use Illuminate\Filesystem\Filesystem;

class TranslatorController extends Controller
{
    //
    protected $user;
    protected $export_file_title;
    protected $export_data;

    function __construct()
    {
        
     //$this->middleware('auth');
     $this->user = Auth::user();
      $this->export_file_title = "Language Translation";
    
    }
    
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
     public function index()
     {
        $user = Auth::user();
        $data['records']      = FALSE;
        $data['locales']  =  File::directories(App::langPath()); //folders
        $data['title'] = "Translator";
        $data['pageTitle'] = $data['title'];
        $data['pageIcon'] = "fa fa-import";
        $view_name = 'import.index';
        return view($view_name, $data);
     }

     public function export()
     {
        $user = Auth::user();
        $data['records']      = FALSE;
        $data['locales']  =  File::directories(resource_path().'/lang/'); //folders
        $data['title'] = "Translator";
        $data['languages'] = [];
        $data['access'] = [];
        $data['header'] = [];
        $data['header'][0][] = "key";

        foreach ($data['locales'] as $key => $value) {
          $access0 = basename($value);
          $data['header'][0][] = $access0; 
        }
          # code...
          $locale = 'en';//Lang::locale();
          $languages = File::allFiles(resource_path().'/lang/'.$locale.'/');
          //$languages = File::allFiles($value);
          foreach ($languages as $k => $langFile) {
            # code...
            $a = File::getRequire($langFile);
            $access1 = basename(explode(".php",$langFile)[0]);
            foreach($a as $key => $value)
            {
              $access2 = $access1.".".$key;
              if(is_array($value)){
                foreach ($value as $key => $val) {
                  # code...
                  if(is_array($val)){
                    foreach ($val as $k => $v) {
                      # code...
                       $data['access'][] = $access2.".".$key.".".$v;
                  
                    }
                  } else 
                        $data['access'][] = $access2.".".$key;
                  

                }
              } else
              $data['access'][] = $access2;
            }
            
          }
          
        
        foreach ($data['access'] as $access) {
          # code...
          $row = [$access];
          $locales = $data['header'][0]; array_shift($locales);
          //var_dump($locales); die();
          foreach ($locales as $key => $locale) {
            # code...
            $trans = Lang::get($access,[],$locale, false);
            
            if($trans != $access)
              $row[] = $trans;
            else 
              $row[] = "";
          }
          $data['header'][] = $row;
          //$row = [
          //print_r($data['header']);
          //die();
        }
        $this->export_data = $data['header'];

        $view_name = 'import.index';
        //print_r($data['header']);
        $this->writer($this->export_file_title,$data['header']);
        return view($view_name, $data);
     }

     public function writer($title,$data){
      Excel::create($title, function($excel) {
        

        $excel->sheet($this->export_file_title, function($sheet) {
          //$sheet->row(1, $this->export_data[0]);
          $data = $this->export_data;
          $cnt = 1;
          foreach ($data as $key => $value) {
           $sheet->appendRow($cnt++, $value);
          }

        });

        })->download('xlsx');

        return TRUE;
     }

    
     public function import(Request $request)
     {
        $columns = array(
        'excel'  => 'bail|required',
        );
         
      try{
        if($request->hasFile('excel')){
          $path = $request->file('excel')->getRealPath();
          $data = Excel::load($path, function($reader) {
          })->get();
          
          
          if(!empty($data) && $data->count()){
            $header = $data->getHeading();
            $locales = $header;
            array_shift($locales);
            foreach ($data as $key => $row) {
                foreach ($row as $locale => $v) {
                  # code...
                  if($locale=="key") continue;
                  $access = $row->key;
                  $this->writeTrans($access,$v,$locale);
                }
            }
          }
        }
       
       
            $message = 'record_added_successfully. ';
        

     }
     catch( \Illuminate\Database\QueryException $e)
     {
       
          flash('flash',$e->getMessage(), 'overlay');
          $message = $e->getMessage();
     }

      
       return redirect(url('import'))->with('flash',$message);
 
     }


     function writeTrans($access,$value,$locale){
        $access = explode(".", $access);
        $fileName = $access[0];
        $fileArray = Lang::get($fileName,[],$locale, true);
        $path = App::langPath() . '/' . $locale . "/$fileName.php";
        if(!file_exists($path)){
          $fileArray = [];
        }
        if(is_array($fileArray)){
          //var_dump($fileName,$value,$locale,$fileArray); exit();
          $indexes = $access;
          array_shift($indexes);
          $l = count($indexes);
          $i = 0;
          /*while ( is_array($tmpV) && $i<=$l) {
            # code...
            $tmpV = $fileArray[$indexes[$i]];
            $i++;
          }*/
          if(is_null($value))
            return;
          if($l == 1 && isset($fileArray[$indexes[0]])) {
            $fileArray[$indexes[0]] = $value;
          }
          if($l == 2 && isset($fileArray[$indexes[0]][$indexes[1]])){
            $fileArray[$indexes[0]][$indexes[1]] = $value;
          }
          if($l == 3 && isset($fileArray[$indexes[0]][$indexes[1]][$indexes[2]])){
            $fileArray[$indexes[0]][$indexes[1]][$indexes[2]] = $value;
          }
          $output = "<?php\n\nreturn " . var_export($fileArray, true) . ";\n";
          $f = new Filesystem();
          $f->put($path, $output);
          
        }
        
     }


     


}
