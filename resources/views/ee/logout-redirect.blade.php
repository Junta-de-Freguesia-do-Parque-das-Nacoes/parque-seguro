<form id="logout-form" action="{{ route('ee.logout') }}" method="POST">
    @csrf
</form>

<script>
    document.getElementById('logout-form').submit();
</script>
