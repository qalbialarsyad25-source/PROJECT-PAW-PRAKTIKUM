@if (session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); border-left: 4px solid #10b981; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; color: #a7f3d0; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <i class="fa-solid fa-circle-check" style="color: #34d399; font-size: 1.2rem;"></i>
        <div style="flex: 1; font-size: 0.92rem;">{{ session('success') }}</div>
    </div>
@endif

@if (session('error'))
    <div style="background: rgba(220, 38, 38, 0.15); border: 1px solid rgba(220, 38, 38, 0.4); border-left: 4px solid #dc2626; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; color: #fecaca; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <i class="fa-solid fa-triangle-exclamation" style="color: #f87171; font-size: 1.2rem;"></i>
        <div style="flex: 1; font-size: 0.92rem;">{{ session('error') }}</div>
    </div>
@endif

@if (session('info'))
    <div style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.4); border-left: 4px solid #3b82f6; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; color: #bfdbfe; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <i class="fa-solid fa-circle-info" style="color: #60a5fa; font-size: 1.2rem;"></i>
        <div style="flex: 1; font-size: 0.92rem;">{{ session('info') }}</div>
    </div>
@endif

@if ($errors->any())
    <div style="background: rgba(220, 38, 38, 0.15); border: 1px solid rgba(220, 38, 38, 0.4); border-left: 4px solid #dc2626; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; color: #fecaca; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px; font-weight: 700;">
            <i class="fa-solid fa-circle-xmark" style="color: #f87171;"></i>
            <span>Terdapat beberapa kesalahan:</span>
        </div>
        <ul style="list-style: disc; margin-left: 28px; font-size: 0.88rem; line-height: 1.5;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
