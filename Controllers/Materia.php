<?php
class Materia extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . base_url);
        }
        parent::__construct();
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Materia");
        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
    }
    public function index()
    {
        $this->views->getView($this, "index");
    }
    public function listar()
    {
        $data = $this->model->getMaterias();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge badge-success">Ativo</span>';
                $data[$i]['acciones'] = '<div>
                <button class="btn btn-primary" type="button" onclick="btnEditarMat(' . $data[$i]['id'] . ');"><i class="fa fa-pencil-square-o"></i></button>
                <button class="btn btn-danger" type="button" onclick="btnEliminarMat(' . $data[$i]['id'] . ');"><i class="fa fa-trash-o"></i></button>
                <div/>';
            } else {
                $data[$i]['estado'] = '<span class="badge badge-danger">Inativo</span>';
                $data[$i]['acciones'] = '<div>
                <button class="btn btn-success" type="button" onclick="btnReingresarMat(' . $data[$i]['id'] . ');"><i class="fa fa-reply-all"></i></button>
                <div/>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        $materia = strClean($_POST['materia']);
        $id = strClean($_POST['id']);
        if (empty($materia)) {
            $msg = array('msg' => 'O campo nome é obrigatório!', 'icono' => 'warning');
        } else {
            if ($id == "") {
                $data = $this->model->insertarMateria($materia);
                if ($data == "ok") {
                    $msg = array('msg' => 'Matéria cadastrada com sucesso!', 'icono' => 'success');
                } else if ($data == "existe") {
                    $msg = array('msg' => 'Esta matéria já existe!', 'icono' => 'warning');
                } else {
                    $msg = array('msg' => 'Erro ao cadastrar a matéria!', 'icono' => 'error');
                }
            } else {
                $data = $this->model->actualizarMateria($materia, $id);
                if ($data == "modificado") {
                    $msg = array('msg' => 'Matéria alterada com sucesso!', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Erro ao alterar a matéria!', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editar($id)
    {
        $data = $this->model->editMateria($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar($id)
    {
        $data = $this->model->estadoMateria(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Matéria dada baixa com sucesso!', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Erro ao dar baixa na matéria!', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar($id)
    {
        $data = $this->model->estadoMateria(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Matéria reingressada com sucesso!', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Erro ao reingressar a matéria!', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function buscarMateria()
    {
        if (isset($_GET['q'])) {
            $valor = $_GET['q'];
            $data = $this->model->buscarMateria($valor);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}
