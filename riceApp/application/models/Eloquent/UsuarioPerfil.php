<?php
class UsuarioPerfil extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'usuario_perfil';
	
	public $timestamps = false;

	public function usuario(){
		return $this->belongsTo('Usuario','id_usuario','id')->with("persona");
	}

}