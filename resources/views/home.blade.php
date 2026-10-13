

@can('isMember')
    <script type="text/javascript">
        window.location = "{{ url('members/dashboard') }}";
    </script>
@endcan

@can('isVisitor')
    <script type="text/javascript">
        window.location = "{{ url('visitors/dashboard') }}";
    </script>
@endcan

<script type="text/javascript">
    window.location = "{{ route('dashboard.index') }}";
</script>
