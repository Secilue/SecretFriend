@extends('layouts.app')

@section('content')
<style>
    .form-group.required .control-label:after {
        content:"*";
        color:red;
    }

    label.required:after {
        color: #cc0000;
        content: "*";
        font-weight: bold;
        margin-left: 5px;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">New Exchange</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div id="field_wrapper" class="form-group required">
                    </div>
                    <div class="text-center">
                        <button type="button" id="addParticipant" class="btn btn-info m-t-2 m-l-2"><i class="fa fa-plus m-1"></i>Add Participant</button>
                        <button type="button" id="create" class="btn btn-success m-t-2"><i class="fa fa-save m-1"></i>Create</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function(){
        var minField = 3;
        var maxField = 12;
        var addButton = $('.add_button');
        var wrapper = $('#field_wrapper');
        var x = 1;
        /* Create minimum participants. */
        for (x; x <= minField; x++) {
            $(wrapper).append('<div class="table-bordered p-2" style="background-color: rgb(237, 247, 250);"><div class="col-md-6 d-inline-block"><label class="control-label required">Name:</label><input class="form-control" type="text" name="name[]" id="name'+ x +'" placeholder="Participant name" required="required"></div><div class="col-md-6 d-inline-block"><label class="control-label">Cellphone number:</label><input class="form-control" type="number" name="cellphoneNUmber[]" id="cellphoneNumber'+ x +'" placeholder="Cellphone number" required="required"></div></div>');
        }
        $(wrapper).on('click', '.remove_participant', function(e){
            e.preventDefault();
            $(this).parent('div').remove(); //Remove field html
            x--;
        });

        $('#addParticipant').on('click', function(e) {
            e.preventDefault();
            if (x <= maxField) {
                $(wrapper).append('<div class="table-bordered p-2" style="background-color: rgb(237, 247, 250);"><div class="col-md-6 d-inline-block"><label class="control-label">Name:</label><input class="form-control" type="text" name="name[]" id="name'+ x +'" placeholder="Participant name" required="required"></div><div class="col-md-5 d-inline-block">Cellphone number:<input class="form-control" type="number" name="cellphoneNUmber[]" id="cellphoneNumber'+ x +'" placeholder="Cellphone number"></div><a href="javascript:void(0);" class="col-md-1 d-inline-block remove_participant" title="Eliminar"><i class="fa fa-fw fa-trash fa-lg text-danger"></i></a></div>');
                x++;
            }
        });
    });

</script>
@endsection
