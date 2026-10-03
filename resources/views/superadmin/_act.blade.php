<form method="POST" action="{{ route('superadmin.admins.action', $a->id) }}" onsubmit="event.preventDefault(); confirmAction(this, @js($confirm), @js(in_array($cls, ['btn-danger', 'btn-warn'])))">
    @csrf
    <input type="hidden" name="action" value="{{ $action }}">
    <button class="btn {{ $cls }}" type="submit">{{ $label }}</button>
</form>