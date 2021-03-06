<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Tolerancia;
use Redirect;
use DB;

class ToleranciasController extends Controller
{
  public function index()
  {
      $tolerancias = $this->arrayTolerancias();

      return view('tolerancias.principal');
  }

  public function lista()
  {
      $tolerancias = $this->arrayTolerancias();
      return view('tolerancias.lista', ['tolerancias' => $tolerancias]);
  }

  public function salvar(Request $request)
  {
      $tolerancia = new Tolerancia();
      $tolerancia = $tolerancia->create($request->all());

      \Session::flash('mensagem_sucesso', 'Tolerância cadastrada com sucesso.');

      if($request->is('tolerancias/salvar'))
        return Redirect::to('tolerancias');
      else
        return Redirect::to('tolerancias/lista/nova');
  }

  public function nova(Request $request)
  {
      $tolerancias = $this->arrayTolerancias();
      return view('tolerancias.lista', ['tolerancias'=>$tolerancias]);
  }

  public function editar($id)
  {
      $tolerancias = $this->arrayTolerancias();
      $tolerancia = Tolerancia::findOrFail($id);

      return view('tolerancias.lista', ['tolerancias'=>$tolerancias, 'tolerancia' => $tolerancia]);
  }

  public function atualizar($id, Request $request)
  {
      $tolerancias = $this->arrayTolerancias();
      $tolerancia = Tolerancia::findOrFail($id);
      $tolerancia->update($request->all());

      \Session::flash('mensagem_sucesso', 'Tolerância atualizada com sucesso.');

      return Redirect::to('tolerancias/lista/'.$tolerancia->id.'/editar');
  }

  public function excluir($id)
  {
      $tolerancia = Tolerancia::findOrFail($id);
      $tolerancia->status = 'I';
      $tolerancia->update();

      \Session::flash('mensagem_sucesso', 'Tolerância excluída com sucesso.');

      return Redirect::to('tolerancias/lista');
  }

  public function buscar(Request $request)
  {
      $filtro = $request->get('buscar');
      $tolerancias = DB::table('tolerancias')
                    ->where([
                      ['descricao', 'like', '%'.$filtro.'%'],
                      ['status', '<>', 'I']
                    ])->get();

      return view('tolerancias.lista', ['tolerancias' => $tolerancias]);
  }

  public function arrayTolerancias()
  {
      $tolerancias_table = Tolerancia::get();
      $tolerancias = array();

      foreach($tolerancias_table as $tolerancia)
      {
        if($tolerancia->status != 'I')
            array_push($tolerancias, $tolerancia);
      }

      return $tolerancias;
  }
}
