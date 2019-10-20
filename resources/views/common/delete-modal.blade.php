<!-- Modal -->
<div class="modal fade modal-danger"  id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{ isset($alert) ? $alert : __('common.confirm-delete') }}</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left flip" id="modal-btn-no" data-dismiss="modal">@lang('common.confirm-no')</button>
                <button type="button" class="btn btn-outline pull-right flip" id="modal-btn-yes">@lang('common.confirm-yes')</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@push('scripts')
    <script type="text/javascript">
        $("#modal-btn-yes").on("click", function(){
            if(formId !== undefined){
                document.getElementById(formId).submit();
            }
        });
        let formId = undefined;
        function setDeleteFormId(_id) {
            formId  = _id;
        }
    </script>
@endpush