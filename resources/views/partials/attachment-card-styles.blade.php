<style>
    .attachment-card {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 1.5rem;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .attachment-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08);
    }
    .attachment-card-preview {
        position: relative;
        aspect-ratio: 16 / 10;
        background: #f8fafc;
        overflow: hidden;
    }
    .attachment-card-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .attachment-card:hover .attachment-card-preview img {
        transform: scale(1.04);
    }
    .attachment-card-preview-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0);
        transition: background 0.3s;
    }
    .attachment-card:hover .attachment-card-preview-overlay {
        background: rgba(15, 23, 42, 0.25);
    }
    .attachment-card-preview-overlay i {
        font-size: 2rem;
        color: #fff;
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 0.3s, transform 0.3s;
    }
    .attachment-card:hover .attachment-card-preview-overlay i {
        opacity: 1;
        transform: scale(1);
    }
    .attachment-card-file-icon {
        aspect-ratio: 16 / 10;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }
</style>
