<?php 

	class Categorias extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(16);

		}

		public function Categorias()
		{
		  if(empty($_SESSION['permisosMod']['r'])){ 
		  	header("Location:" .base_url().'/dashboard');
			}
			$data['page_tag'] = "Categorias";
			$data['page_title'] = "CATEGORIAS <small>Tienda Virtual</small>";
			$data['page_name'] = "categorias";
	        $data['page_functions_js'] = "functions_categorias.js";
			$this->views->getView($this,"categorias",$data);
		}

	    public function setCategoria(){		

				if($_POST){

					if(empty($_POST['txtNombre']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus']) )
					{
						$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');

					}else{

	 		    	$intIdcategoria= intval($_POST['idCategoria']);
			    	$strCategoria =  strClean($_POST['txtNombre']);
					$strDescipcion = strClean($_POST['txtDescripcion']);
					$intStatus = intval($_POST['listStatus']);

					$foto   	 	= $_FILES['foto'];
					$nombre_foto 	= $foto['name'];
					$type 		 	= $foto['type'];
					$url_temp    	= $foto['tmp_name'];
					$imgPortada 	= 'portada_categoria.png';

					if($nombre_foto != ''){
						$imgPortada = 'img_'.md5(date('d-m-Y H:m:s')).'.jpg';
					}

					if($intIdcategoria == 0)
					{
						//Crear
						$request_cateria = $this->model->insertCategoria($strCategoria,$strDescipcion,$imgPortada,$intStatus);
						$option = 1;
					}else{
						//Actualizar
						$request_cateria = $this->model->updateCategoria($intIdrol, $strRol, $strDescipcion, $intStatus);
						$option = 2;
					}

					if($request_cateria > 0 )
					{
						if($option == 1)
						{
							$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
							if($nombre_foto != ''){ uploadImage($foto,$imgPortada); }
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
						}
					}else if($request_cateria == 'exist'){
						
						$arrResponse = array('status' => false, 'msg' => '¡Atención! La Categoría ya existe.');
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}

				 }  
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);     

              }
                
               die();

	     }	

	  public function getCategorias()
		{
				$arrData = $this->model->selectCategorias();
				for ($i=0; $i < count($arrData); $i++) {
					$btnView = '';
					$btnEdit = '';
					$btnDelete = '';

					if($arrData[$i]['status'] == 1)
					{
						$arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
					}else{
						$arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
					}

					if($_SESSION['permisosMod']['r']){
						$btnView = '<button class="btn btn-info btn-sm" onClick="fntViewInfo('.$arrData[$i]['idcategoria'].')" title="Ver categoría"><i class="far fa-eye"></i></button>';
					}
					if($_SESSION['permisosMod']['u']){
						$btnEdit = '<button class="btn btn-primary  btn-sm" onClick="fntEditInfo(this,'.$arrData[$i]['idcategoria'].')" title="Editar categoría"><i class="fas fa-pencil-alt"></i></button>';
					}
					if($_SESSION['permisosMod']['d']){	
						$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idcategoria'].')" title="Eliminar categoría"><i class="far fa-trash-alt"></i></button>';
					}
					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			
			die();
		}	

	    public function getCategoria(int $idcategoria)
		{

			$intIdcategoria = intval($idcategoria);
			if($intIdcategoria > 0)
			{
				$arrData = $this->model->selectCategoria($intIdcategoria);
				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrData['url_portada'] = media().'/images/uploads/'.$arrData['portada'];
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}

			die();
     }   

	}

?>