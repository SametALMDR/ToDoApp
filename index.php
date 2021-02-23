<?php

define('WEB_ROOT',$_SERVER['REQUEST_SCHEME'].'://'.dirname($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']));
define('CURR_URL',$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
define('DS',DIRECTORY_SEPARATOR);

include 'classes/Config.php';
include 'classes/ToDo.php';

$app    = new ToDo("todos");

if(isset($_POST['create-new-todo'])){
    $app->insertTodo($_POST['job'],$_POST['priority'],$_POST['date']);
}
if(isset($_POST['update-todo'])){
    $app->setTodo($_POST['up-id'],$_POST['up-job'],$_POST['up-priority'],$_POST['up-date']);
}
if(isset($_POST['delete-todo'])){
    $app->deleteTodo($_POST['id']);
}
if(isset($_POST['complete-todo'])){
    $app->setStatus($_POST['id'],true);
}
if(isset($_POST['uncomplete-todo'])){
    $app->setStatus($_POST['id'],false);
}
if(isset($_GET['orderby'])){
    $app->setOrderBy($_GET['orderby']);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$app->appConfig['appName']?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1"
          crossorigin="anonymous">
    <link href="main.css" rel="stylesheet">
</head>
<body>

<div class="container-lg">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="todo-box">
                <div class="d-flex justify-content-between p-4 pb-0 text-white">
                    <div>
                        <h1><?=$app->appConfig['appName']?></h1>
                        <p class="mb-0"><?=$app->appConfig['appDescription']?></p>
                    </div>
                    <div>
                        <div class="version"><?=$app->appConfig['appVersion']?></div>
                    </div>
                </div>
                <form class="row p-4" action="" method="post">
                    <div class="col-sm-6">
                        <input type="text" name="job" class="form-control" placeholder="Add a new job to do..." required>
                    </div>
                    <div class="col-sm-2">
                        <select name="priority" class="form-control">
                            <option value="1">Low</option>
                            <option value="2">Medium</option>
                            <option value="3">High</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="col-sm-1">
                        <button type="submit" name="create-new-todo" class="btn btn-block w-100 create-btn">Add</button>
                    </div>
                </form>
                <div class="todos p-4 pt-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="text-white mb-0">To Do List </h4>
                        <div class="d-flex align-items-center">
                            <label class="text-white px-2">Sort: </label>
                            <select class="form-control" id="orderby">
                                <option value="<?=WEB_ROOT?>?orderby=1" <?=$app->getOrderBy()==1?"selected":""?>>Low to High</option>
                                <option value="<?=WEB_ROOT?>?orderby=2" <?=$app->getOrderBy()==2?"selected":""?>>High to Low</option>
                            </select>
                        </div>
                    </div>
                    <?php
                        if(count($app->getTodos())===0){
                    ?>
                    <div class="single-row p-3 text-center">
                        <div class="row align-items-center">
                            <div class="col-sm-12">
                                Currently, you have nothing to do.
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <?php
                        foreach ($app->getTodos() as $key => $todo) {
                    ?>
                    <div class="single-row <?=$key !== array_key_last($app->getTodos())?"mb-3":""?>">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <p class="px-2 mb-0 <?=($todo->completed)?"text-decoration-line-through":""?>"><?=$todo->job?></p>
                            </div>
                            <div class="col-sm-2">
                                <p class="px-2 mb-0 <?=($todo->completed)?"text-decoration-line-through":""?>"><?=$app->getPriorityMean($todo->priority)?></p>
                            </div>
                            <div class="col-sm-2">
                                <p class="px-2 mb-0 <?=($todo->completed)?"text-decoration-line-through":""?>"><?=$todo->date?></p>
                            </div>
                            <div class="col-sm-2 btn-actions">
                                <button class="btn btn-white rounded-0 update-btn"
                                        data-id="<?=$todo->id?>"
                                        data-job="<?=$todo->job?>"
                                        data-priority="<?=$todo->priority?>"
                                        data-date="<?=$todo->date?>"
                                >
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                <form action="<?=CURR_URL?>" method="post" class="d-inline-block mb-0">
                                    <input type="hidden" name="id" value="<?=$todo->id?>">
                                        <button class="btn btn-white rounded-0 complete-btn" name="<?=$todo->completed?"un":""?>complete-todo">
                                            <i class="bi <?=$todo->completed?"bi-reply":"bi-check2-all"?>"></i>
                                        </button>
                                    <button class="btn btn-white rounded-0 delete-btn" name="delete-todo"><i class="bi bi-x"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update a Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row p-4" action="" method="post">
                    <input type="hidden" name="up-id">
                    <div class="col-sm-12 mb-4">
                        <input type="text" name="up-job" class="form-control" placeholder="Add a new job to do..." required>
                    </div>
                    <div class="col-sm-6">
                        <select name="up-priority" class="form-control up-priority">
                            <option value="1">Low</option>
                            <option value="2">Medium</option>
                            <option value="3">High</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <input type="date" name="up-date" class="form-control" required>
                    </div>
                    <div class="col-sm-12 mt-4">
                        <button type="submit" name="update-todo" class="btn btn-block w-100 create-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW"
        crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
        integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
        crossorigin="anonymous">
</script>
<script>
    var myModal = new bootstrap.Modal(document.getElementById('updateModal'), {
        keyboard: false
    })
    $('.update-btn').on('click',function (){
        $('input[name=up-id]').val($(this).data('id'))
        $('input[name=up-job]').val($(this).data('job'))
        $(".up-priority option").removeAttr('selected', 'selected');
        $(".up-priority option[value="+$(this).data('priority')+"]").attr('selected', 'selected');
        let arr = $(this).data('date').split(".")
        $('input[name=up-date]').val(arr[2]+'-'+arr[1]+'-'+arr[0])
        myModal.show()
    })
    $('#orderby').on('change',function (){
        window.location = this.options[this.selectedIndex].value
    })
</script>
</body>
</html>
