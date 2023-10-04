<?php
try{
$conn= new PDO ('mysql:host=localhost;dbname=apptodolist','root', '');
}catch(PDOException $e){
echo "Error de conexión" .$e->getMessage();
}

//Actualizar estado tarea

if(isset($_POST['id'])){
    $id=$_POST['id'];
    $completado=(isset($_POST['completado']))?1:0;
    $sql = "UPDATE tareas SET completado=? WHERE id=?";
    $sentencia =$conn->prepare($sql);
    $sentencia->execute([$completado, $id]);

}

//Agregar tareas

if(isset($_POST['agregar_tarea']))
{
    $tarea = $_POST['tarea'];
    $fecha = $_POST['fecha']; 
    $categoria = $_POST['categoria']; 
    $sql = 'INSERT INTO tareas (tarea, fecha, categoria) VALUES (?, ?, ?)'; 
    $sentencia = $conn->prepare($sql);
    $sentencia->execute([$tarea, $fecha, $categoria]);
}

//Eliminar tareas

if(isset($_GET ['id'])){
    $id = $_GET['id'];
    $sql = "DELETE FROM tareas WHERE id=?";
    $sentencia =$conn->prepare($sql);
    $sentencia->execute([$id]);
}

// Filtrar tareas

if (isset($_POST['filtro_categoria'])) {
    $filtro_categoria = $_POST['filtro_categoria'];
    if ($filtro_categoria !== "") {
        $sql = "SELECT * FROM tareas WHERE categoria=?";
        $sentencia = $conn->prepare($sql);
        $sentencia->execute([$filtro_categoria]);
        $registros = $sentencia->fetchAll();

        ob_start();
        foreach ($registros as $registro) : ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <form class="form-check" action="" method="post">
                    <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
                    <input class="form-check-input" type="checkbox" name="completado" value="<?php echo $registro['completado']; ?>" onChange="this.form.submit()" id="tarea_<?php echo $registro['id']; ?>" <?php echo ($registro['completado'] == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label <?php echo ($registro['completado'] == 1) ? 'tachado' : ''; ?>" for="checked" style="<?php echo ($registro['completado'] == 1) ? 'text-decoration: line-through;' : ''; ?>"><?php echo $registro['tarea']; ?></label>
                </form>
                <div class="d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="ms-2">Fecha: <?php echo $registro['fecha']; ?></div>
                        <div class="ms-2">Categoría: <?php echo $registro['categoria']; ?></div>
                        <a href="?id=<?php echo $registro['id']; ?>"><span class="badge bg-danger ms-2">x</span></a>
                    </div>
                </div>
            </li>
        <?php endforeach; ?> 

<?php
        $html_tareas = ob_get_clean();
        echo json_encode(['html' => $html_tareas]);
        exit;
    }   

}


$sql="SELECT * FROM tareas";
$registros=$conn->query($sql);

