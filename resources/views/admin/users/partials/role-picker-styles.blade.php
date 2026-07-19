<style>
.role-picker-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(132px, 1fr));
    gap: 0.75rem;
    margin-top: 0.25rem;
}

.role-picker-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem 0.625rem;
    min-height: 108px;
    border-radius: 1rem;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    cursor: pointer;
    transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    text-align: center;
}

.role-picker-card:hover:not(.is-locked) {
    border-color: var(--role-accent, #94a3b8);
    background: #f8fafc;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.role-picker-card.is-selected {
    border-color: var(--role-accent, #f59e0b);
    background: color-mix(in srgb, var(--role-accent, #f59e0b) 8%, #ffffff);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--role-accent, #f59e0b) 22%, transparent);
}

.role-picker-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #64748b;
    font-size: 1.25rem;
    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}

.role-picker-card.is-selected .role-picker-icon {
    background: var(--role-accent, #f59e0b);
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
}

.role-picker-text {
    display: block;
    font-size: 0.75rem;
    font-weight: 800;
    color: #475569;
    line-height: 1.35;
}

.role-picker-card.is-selected .role-picker-text {
    color: #0f172a;
}

.role-picker-card.is-locked:not(.is-selected) {
    cursor: default;
    pointer-events: none;
    opacity: 0.45;
    filter: grayscale(0.25);
}

.role-picker-card.is-locked.is-selected {
    cursor: default;
    pointer-events: none;
}

.role-picker-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.125rem;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 800;
    color: var(--role-accent, #f59e0b);
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.role-picker-badge i {
    font-size: 0.625rem;
}

.role-picker-check {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    font-size: 1.125rem;
    line-height: 1;
    color: var(--role-accent, #f59e0b);
    opacity: 0;
    transform: scale(0.85);
    transition: opacity 0.2s ease, transform 0.2s ease;
    pointer-events: none;
}

.role-picker-card.is-selected .role-picker-check {
    opacity: 1;
    transform: scale(1);
}

.role-picker-card:focus-within:not(.is-locked) {
    outline: 2px solid color-mix(in srgb, var(--role-accent, #64748b) 40%, transparent);
    outline-offset: 2px;
}
</style>
