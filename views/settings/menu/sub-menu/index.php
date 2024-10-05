<?php
include('../../../../inc/app_settings.php');
require_once('../../../../inc/helpers.php');
// require_once('../../../../models/Menu.php');

$helpers = new Helpers();

define('PAGE_TITLE', 'Sub-menu');

if(!$helpers->checkSession()) {
    $helpers->redirectLogin();
    return;
}

$menu_id = $_GET['id'];

$decryptedmenuId = $helpers->encryptDecrypt($menu_id, $action = 'decrypt'); 

include_once '../../../../templates/header.php';
include_once '../../../../templates/sidebar.php';

$resMenu = $menu->getWhere("AND id = $decryptedmenuId");
$menuName = "";
if(!empty($resMenu)) {
    $menuName = $resMenu[0]['name'];
}

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
                        Menu - <?php echo $menuName ?>
                        <span class="float-end">
                            <button class="btn btn-outline-secondary btn-rounded btn-icon btn-sm" id="btn-add">
                                <i class="mdi mdi-plus-outline text-info"></i>
                            </button>
                        </span>
                    </h4>
                    <table class="table" id="tbl-data">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Url</th>
                                <th>Icon</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th>Created </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
        <!-- MODAL 1 -->
        <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add-user-title">Add <?php echo PAGE_TITLE ?></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="frm-menu" method="post" data-parsley-validate="">
                            <input type="hidden" name="action_type" id="action_type">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" id="menu_id" name="menu_id" value="<?php echo $decryptedmenuId;?>" />
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" data-parsley-required="" data-parsley-required-message="Name is required" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="value">URL</label>
                                <input type="text" class="form-control" id="url" name="url" data-parsley-required="" data-parsley-required-message="URL is required." autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="value">Icon</label>
                                <input type="text" class="form-control" id="icon" name="icon" data-parsley-required="" data-parsley-required-message="Icon is required." autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="value">Active Keyword</label>
                                <input type="text" class="form-control" id="active_keyword" name="active_keyword" data-parsley-required="" data-parsley-required-message="Active keyword is required for which menu should be active.">
                            </div>

                            <div class="form-group">
                                <label for="value">Sort</label>
                                <input type="number" class="form-control" id="sort" name="sort" min="1" data-parsley-required="" data-parsley-required-message="Sort is required.">
                            </div>                            
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="status" name="status"> Status </label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="btn-save" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
                             
<?php
    include_once '../../../../templates/footer.php';
?>
<script>
    $(document).ready(function(){
        var table = $('#tbl-data').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo BASE_URL ?>/api/menu/sub-menu/get.php',
                    type: 'POST',
                    data : {
                        'id' : '<?php echo $menu_id ?>'
                    }
                },
                "columnDefs": [ {
                    "targets": [6],
                    "orderable": false
                } ],
                "order": []
            });

            $('#btn-save').click(function(){
                if(!$('form#frm-menu').parsley().validate()) {
                    return;
                }
                var msg = $('.error-message');
                $.ajax({
                    url : '<?php echo BASE_URL ?>/api/menu/sub-menu/dml.php',
                    type : 'post',
                    data : $('#frm-menu').serialize(),
                    success : function(data) {
                        var json = $.parseJSON(data);

                        if(json['code'] == 0) {
                            msg.html('<div class="alert alert-success">'+ json['message'] +'</div>');
                            $('#modal-add').modal('hide');
                            table.ajax.reload();
                        } else {
                            msg.html('<div class="alert alert-danger">'+ json['message'] +'</div>');
                        }
                    }
                })
                return false;
            })

            $('#btn-add').click(function(){
                // Reset the form to remove the validation error
                $('#frm-menu').parsley().reset();
                $('#action_type').val('add');
                $('#id').val('');
                $('#frm-menu')[0].reset();
                $('#modal-add').modal('show');
                $('#modal-title').text('Add Menu');
            })
        });


        //for edit
        $(document).on('click', '.btn-edit' ,function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var url = $(this).data('url');
                var icon = $(this).data('icon');
                var sort = $(this).data('sort');
                var status = $(this).data('status');
                var activekeyword = $(this).data('active-keyword');
                
                // Reset the form to remove the validation error
                $('#frm-menu').parsley().reset();

                $('#id').val(id);
                $('#action_type').val('update');
                $('#name').val(name);
                $('#url').val(url);
                $('#icon').val(icon);
                $('#sort').val(sort);
                $('#status').val(status);
                $('#active_keyword').val(activekeyword);
                
                if(status == 'Y'){
                    $('#active').prop('checked', true);
                }
                else {
                    $('#active').prop('checked', false);

                }
            

                $('#modal-add').modal('show');
                $('#modal-title').text('Edit Menu');
            })

            //for delete
            $(document).on('click', 'a.btn-delete', function(){
                var id = $(this).data('id');
                var name = $(this).data('name');

                if(confirm('Are you sure you want delete name: ' + name + '?'))
                {
                    $.ajax({
                        url : '<?php echo BASE_URL ?>/api/menu/sub-menu/dml.php',
                        type : 'post',
                        data : { action_type : 'delete', 'id' : id },
                        success : function(data) {
                            var json = $.parseJSON(data);
                            if(json['code'] == 0) {
                                alert(json['message']);
                                $('#tbl-data').DataTable().ajax.reload();
                            } else {
                                alert(json['message']);
                            }
                        }
                    })
                }
            })


            //clearing modal styles
            $('body').on('hidden.bs.modal', '.modal', function () {
                console.log("modal closed");
            
                $("#error-message").html("");
                $("#name").add("#url").add("#icon").add("#sort").css({ 'background-color' : 'white', 'border-color' : '', 'color' : 'black' });
                $('#frm-menu').parsley().reset();

        });
</script>