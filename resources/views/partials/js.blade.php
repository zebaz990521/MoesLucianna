  <!-- JAVASCRIPT -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
  <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
  <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
{{--   <script src="{{ asset('assets/js/plugins.js') }}"></script> --}}

<script>
    (document.querySelectorAll("[toast-list]") || document.querySelectorAll("[data-choices]") || document.querySelectorAll("[data-provider]")) && (document.writeln("<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/toastify-js'><\/script>"), document.writeln("<script type='text/javascript' src='{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js')}}'><\/script>"), document.writeln("<script type='text/javascript' src='{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}'><\/script>"));
</script>

  <!-- Aplicación JS -->
  <script src="{{ asset('assets/js/app.js') }}"></script>



  <!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!--select2 cdn-->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="{{ asset('assets/js/pages/select2.init.js')}}"></script>
