<?php
include('../../inc/app_settings.php');
require_once('../../inc/helpers.php');
require_once('../../models/Subjects.php');
require_once('../../models/Questions.php');

$helpers = new Helpers();
$subjects = new Subjects();
$questions = new Questions();

define('PAGE_TITLE', 'Questions');

if(!$helpers->checkSession()) {
    $helpers->redirectLogin();
    return;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';

if(empty($id)) {
    echo json_encode(['code' => 5, 'message' => 'Invalid request']);
    return;
}

$questionId = $helpers->encryptDecrypt($id, 'decrypt');

if(!is_numeric($questionId)) {
    echo json_encode(['code' => 4, 'message' => 'Invalid request']);
    return;
}

$resSubjects = $subjects->getWhere("AND is_active = 'Y'", "name asc");

$resQuestions = $questions->getWhere("AND id = $questionId");

if(empty($resQuestions)) {
    echo json_encode(['code' => 3, 'message' => 'Invalid request']);
    return;
}

// print_r($resQuestions);

include_once '../../templates/header.php';
include_once '../../templates/sidebar.php';
?>
<style>
    .btn.btn-icon {
        width: 30px;
        height: 30px;
    }
    .card .card-title {
        border-bottom: 1px solid #ccc;
        padding-bottom: 13px;
    }
    .form-group {
        margin-bottom: 0.75rem;
    }

    .form-control, .typeahead, .tt-query, .tt-hint, .select2-container--default .select2-selection--single .select2-search__field, .select2-container--default .select2-selection--single{
        padding: 8px 8px;
    }

    .table th, .table td{
        padding: 5px 5px 5px 5px;
    }

    .table > :not(caption) > * > *{
        padding: 5px 5px 5px 5px;
    }

    table.dataTable{
        margin-top: 21px !important;
    }

    .parsley-errors-list {
        margin: 0px;
    }

    .parsley-errors-list {
        font-size: 0.8rem;
    }
    .password-area {
        display: none;
    }
</style>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> <?php echo PAGE_TITLE ?> </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo PAGE_TITLE ?>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <?php echo PAGE_TITLE ?>
                    </h4>
                    <form id="frm-question" method="post" data-parsley-validate="" enctype="multipart/form-data">
                        <input type="hidden" name="action_type" id="action_type" value="update">
                        <input type="hidden" name="id" id="id" value="<?php echo $resQuestions[0]['id'] ?>">
                        <div class="form-group">
                            <label for="subject_id">Subject</label>
                            <select class="form-control" id="subject_id" name="subject_id" data-parsley-required="" data-parsley-required-message="Subject is required">
                                <option value="">Select</option>
                                <?php foreach($resSubjects as $subject): ?>
                                    <option value="<?php echo $subject['id'] ?>" <?php echo ($resQuestions[0]['subject_id'] == $subject['id']) ? 'selected' : '' ?>>
                                        <?php echo $subject['course_no'] . ' - ' . $subject['name'] . ' - ' . $subject['description'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>    
                        </div>
                        <div class="form-group">
                            <label for="value">Question</label>
                            <textarea class="form-control" id="question" name="question"><?php echo $resQuestions[0]['question'] ?></textarea>
                        </div>       
                        <div class="form-check form-check-flat form-check-primary">
                            <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo ($resQuestions[0]['is_active'] == 'Y') ? 'checked' : '' ?>> Status </label>
                        </div>
                        <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/views/questions/index.php">Cancel</a>
                        <button type="submit" id="btn-save" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>        
                             
<?php
    include_once '../../templates/footer.php';
?>
<script>
    $(document).ready(function(){
        $('#question').summernote();

        $("#frm-question").on('submit',(function(e) {
                e.preventDefault();
                $.ajax({
                        url: '<?php echo BASE_URL ?>/api/questions/dml.php',
                    type: "POST",
                    data:  new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(data) {
                        // if(data=='invalid')
                        // {
                        // // invalid file format.
                        // $("#err").html("Invalid File !").fadeIn();
                        // }
                        // else
                        // {
                        // // view uploaded file.
                        // $("#preview").html(data).fadeIn();
                        // $("#form")[0].reset(); 
                        // }
                        // },
                        // error: function(e) 
                        // {
                        // $("#err").html(e).fadeIn();
                        // }   
                        }       
                    });
                }));
        // $('#btn-save').click(function(){
        //     if(!$('form#frm-question').parsley().validate()) {
        //         return;
        //     }
        //     var form = $("#frm-question");

        //     // you can't pass jQuery form it has to be JavaScript form object
        //     var formData = new FormData(form[0]);
        //     var msg = $('.error-message');
        //     $.ajax({
        //         url : '<?php echo BASE_URL ?>/api/questions/dml.php',
        //         type : 'post',
        //         data : formData,
        //         success : function(data) {
        //             var json = $.parseJSON(data);

        //             if(json['code'] == 0) {
        //                 msg.html('<div class="alert alert-success">'+ json['message'] +'</div>');
        //                 table.ajax.reload();
        //             } else {
        //                 msg.html('<div class="alert alert-danger">'+ json['message'] +'</div>');
        //             }
        //         }
        //     })
        //     return false;
        // })
    })
</script>