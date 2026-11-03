@props(['status', 'label' => null])
<span class="status status-{{ $status }}">{{ $label ?? ucfirst(str_replace('_', ' ', $status)) }}</span>
