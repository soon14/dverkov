<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Functions_images extends CI_Controller {

public function __construct()
	{
	session_start();
	parent:: __construct();
	$this->load->library('image_moo');
	$this->load->model('producers_id_model');
	$this->load->model('producers_id_logo');
	$this->load->model('materials_model');
	$this->load->model('styles_model');
	$this->load->model('photos_model');
	require('server/FirePHP.class.php');
	$firephp = FirePHP::getInstance(true);
	$firephp -> fb("hello world! i'm warning you!",FirePHP::WARN);
	} 
	public function index(){
	if(!empty($_FILES)) {
	$prod_id=$_GET['prod_id'];
	$img_height=$_GET['lheight'];
	$img_width=$_GET['lwidth'];
	$uploads_dir=$_GET['upload_dir'];
	$ltype=$_GET['ltype'];
	// ���� ������� ����� ������� ������ $_FILES
    /* echo 'Contents of $_FILES:<br/><pre>'.print_r($_FILES, true).'</pre>';273 74*/
	header("Content-type: text/javascript");
    $file = $_FILES['my-pic'];
	$file_name=$file['name'];
	$tetos = strrpos($file_name,'.');
	$file_name = substr($file_name, 0, $tetos).'('.md5(uniqid(rand(),1)).').'.substr($file_name, $tetos + 1);
	$tmp_name=$file['tmp_name'];
	$upload=$uploads_dir.'/'.$file_name;
	if (move_uploaded_file($tmp_name, "$uploads_dir/$file_name")){
	//������ ������ �� ������ 1000px
	if($ltype!='video'){
	$image_info = GetImageSize($upload);
	$ratio_img=$image_info[0]/$image_info[1];
	if($img_height>$image_info[1] or $img_width>$image_info[0]){
	
	//���� ����� �� ���� ����������� �����
	$config['image_library'] = 'gd2';
	$config['source_image'] = $upload;
	$config['create_thumb'] = FALSE;
	$config['maintain_ratio'] = TRUE;
	$config['width'] = $img_width;
	$config['height'] = $img_height;
	$this->load->library('image_lib', $config);
	$this->image_lib->resize();
	
	}
	else{
	$this->image_moo
	->load($upload)
	->resize($img_width,$img_height)
	->save($upload,TRUE);
	}
	$ar['width']=$img_width;
	$ar['height']=$img_width/$ratio_img;
	}
	

}
if($ltype=='logo_edit'){
$trimmed = ltrim($upload, ".");
$_SESSION['logo']=$trimmed;
$data['logo']=$_SESSION['logo'];
$this->producers_id_logo->update($data,'id',$prod_id);
unset($_SESSION['logo']);
}
else if ($ltype=='video'){
$video_type= end(explode(".",$upload));;
$trimmed = ltrim($upload,".");
$data['path']=$trimmed;
$data['id_door']=$prod_id;
$_SESSION['gal_video']['path'][]=$trimmed;
$_SESSION['gal_video']['type'][]=$video_type;
$ar['path']=$trimmed;
$ar['gall']='video'; 
echo json_encode($ar);
}

else if($ltype=='material_edit'){
$trimmed = ltrim($upload, ".");
$_SESSION['mat_photo']=$trimmed;
$data['image']=$_SESSION['mat_photo'];
$this->materials_model->update($data,'id',$prod_id);
unset($_SESSION['mat_photo']);
$ar['mat']='material';
echo json_encode($ar);
}
else if($ltype=='style_edit'){
$trimmed = ltrim($upload, ".");
$_SESSION['style_photo']=$trimmed;
$data['image']=$_SESSION['style_photo'];
$this->styles_model->update($data,'id',$prod_id);
unset($_SESSION['style_photo']);
$ar['style']='style';
echo json_encode($ar);
}
else if($ltype=='door'){
$trimmed = ltrim($upload, ".");
$data['path']=$trimmed;
$data['id_door']=$prod_id;
$this->photos_model->insert($data);
}
else if($ltype=='material'){
$trimmed = ltrim($upload, ".");
$data['path']=$trimmed;
$data['id_door']=$prod_id;
$ar['mat']='material';
$_SESSION['mat_photo']=$trimmed;
$ar['mat_photo']=$_SESSION['mat_photo'];
echo json_encode($ar);
/* $this->materials_model->insert($data); */
}
else if($ltype=='style'){
$trimmed = ltrim($upload,".");
$data['path']=$trimmed;
$data['id_door']=$prod_id;
$ar['mat']='style';
$_SESSION['style_photo']=$trimmed;
$ar['style_photo']=$_SESSION['style_photo'];
echo json_encode($ar);
/*$this->materials_model->insert($data);*/
}
else if($ltype=='gallery'){ 
$trimmed = ltrim($upload,".");
$data['path']=$trimmed;
$data['id_door']=$prod_id;
$ar['gall']='gallery';
$_SESSION['gal_photo'][]=$trimmed;
$ar['path']=$trimmed;
$ar['gall']='gallery';//first amen   *����� ��� ����� ���� 
echo json_encode($ar);
}

else if($ltype=='color'){
$trimmed = ltrim($upload,".");
$data['path']=$trimmed;
$data['id_door']=$prod_id;
$ar['mat']='color';
$_SESSION['color_photo']=$trimmed;
$ar['color_photo']=$_SESSION['color_photo'];
echo json_encode($ar);
/*$this->materials_model->insert($data);*/
}
else if($ltype=='door_add'){
$trimmed = ltrim($upload, ".");
$_SESSION['photo_door'][]=$trimmed;
$ar['photo_door']=true;
$ar['get']=true;
echo json_encode($ar);
}
else if($ltype=='collection_add'){
$trimmed = ltrim($upload, ".");
$_SESSION['photo_door'][]=$trimmed;
$ar['photo_door']=true;
$ar['coll']=true;
echo json_encode($ar);
}
else if($ltype=='logo'){
$trimmed=ltrim($upload,".");
$_SESSION['logo']=$trimmed;
$trimmed=ltrim($uploads_dir,".");
$ar['dir']=$trimmed;
$ar['file']=$file_name;
echo json_encode($ar);
}

}



}


}	
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */