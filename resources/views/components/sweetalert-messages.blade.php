{{-- 
    SweetAlert Session Messages Component
    Automatically displays session messages using SweetAlert2
    
    Usage: Include this component in your layout or view:
    <x-sweetalert-messages />
    
    NOTE: Uses setTimeout to ensure CDSS and other critical JS initializes first
--}}

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof showSuccess === 'function') {
                    showSuccess(@json(session('success')));
                }
            }, 100);
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof showError === 'function') {
                    showError(@json(session('error')));
                }
            }, 100);
        });
    </script>
@endif

@if (session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof showWarning === 'function') {
                    showWarning(@json(session('warning')));
                }
            }, 100);
        });
    </script>
@endif

@if (session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof showInfo === 'function') {
                    showInfo(@json(session('info')));
                }
            }, 100);
        });
    </script>
@endif


{{-- ** taena ito pala yung error letse 3 days ako dito ah ** --}}

{{-- 
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof showError === 'function') {
                    @if ($errors->count() === 1)
                        showError(@json($errors->first()), 'Validation Error');
                    @else
                        showError(@json(implode("\n", $errors->all())), 'Validation Errors');
                    @endif
                }
            }, 100);
        });
    </script>
@endif
--}}
