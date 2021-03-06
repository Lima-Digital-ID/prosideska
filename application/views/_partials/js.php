<script src="<?php echo base_url() ?>assets/js/core/jquery.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/core/popper.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/core/bootstrap.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<!--  Google Maps Plugin    -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Chart JS -->
<script src="<?php echo base_url() ?>assets/js/plugins/chartjs.min.js"></script>
<!--  Notifications Plugin    -->
<script src="<?php echo base_url() ?>assets/js/plugins/bootstrap-notify.js"></script>
<script src="<?php echo base_url() ?>assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>
<!-- Datatables -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!-- <script src="<?php echo base_url() ?>assets/summernote/summernote.min.js"></script> -->

<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
  $(document).ready(function() {
    $('#summernote').summernote({
        tabsize: 2,
        height: 100
      });

    $(document).on('click', '.editPermintaan', function(e) {
      e.preventDefault();
      let id = $(this).attr('data-id');
      $('#id').val(id);
      var _html = '';
      if (id) {
        $.ajax({
          type: 'GET',
          url: "<?php echo base_url() ?>ListPermintaan/update/" + id,
          dataType: 'json',
          // data: {id: id},
          success: function(data) {
            $.each(data, function(key, value) {
              var data = JSON.parse(value.form_data);
              for (let [key, value] of Object.entries(data)) {
                _html += '<div class="form-group">'
                _html += '<label for="' + key + '"> ' + capitalizeFirstLetter(key.replaceAll('_', ' ')) + '</label>'
                _html += '<input type="text" name="' + key + '" value="' + value + '" class="form-control mb-3" id="' + key + '</input>'
                _html += '</div>';
              }
            })

            $('#form_data').append(_html)
          }
        })
      } else {
        $('#form_data').remove()
      }
    });

    function capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    }

    $('#example').DataTable({
      "pageLength": 5,
      "bLengthChange": false,
    });

    $("#show_hide_password a").on('click', function(event) {
      event.preventDefault();
      if ($('#show_hide_password input').attr("type") == "text") {
        $('#show_hide_password input').attr('type', 'password');
        $('#show_hide_password i').addClass("fa-eye-slash");
        $('#show_hide_password i').removeClass("fa-eye");
      } else if ($('#show_hide_password input').attr("type") == "password") {
        $('#show_hide_password input').attr('type', 'text');
        $('#show_hide_password i').removeClass("fa-eye-slash");
        $('#show_hide_password i').addClass("fa-eye");
      }
    });
    $('.select2').select2({
      closeOnSelect: false
    });

  });
</script>
<?php if ($this->uri->segment(1) == 'welcome') : ?>
  <script src="<?php echo base_url() ?>assets/demo/demo.js"></script>
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/assets-for-demo/js/demo.js
      demo.initChartsPages();
    });
    <?php endif ?>
  </script>