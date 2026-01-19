{{-- Delete confirmation modal (SB Admin 2 style) --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="deleteUserForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmLabel">Confirm Delete</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deleteUserName"></strong>?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="submit">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Script: isi action & nama saat modal terbuka --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // jQuery if available (SB Admin 2 usually includes jQuery)
    if (window.jQuery) {
        $('#deleteConfirmModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var action = button.data('action');
            var name = button.data('name');
            $(this).find('#deleteUserForm').attr('action', action);
            $(this).find('#deleteUserName').text(name);
        });
    } else {
        // vanilla fallback
        var modal = document.getElementById('deleteConfirmModal');
        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) return;
            var action = button.getAttribute('data-action');
            var name = button.getAttribute('data-name');
            document.getElementById('deleteUserForm').setAttribute('action', action);
            document.getElementById('deleteUserName').textContent = name;
        });
        // also attach click listeners to open buttons if bootstrap events not available
        document.querySelectorAll('[data-toggle="modal"][data-target="#deleteConfirmModal"]').forEach(function(btn){
            btn.addEventListener('click', function(){
                var action = btn.getAttribute('data-action');
                var name = btn.getAttribute('data-name');
                document.getElementById('deleteUserForm').setAttribute('action', action);
                document.getElementById('deleteUserName').textContent = name;
            });
        });
    }
});
</script>