<script>
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        const currentCat = document.getElementById('category_id')?.value;
        const parentSelect = document.getElementById('new_category_parent_id');
        if (parentSelect) {
            parentSelect.value = currentCat || '';
        }
        document.getElementById('new_category_name').focus();
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('new_category_name').value = '';
        const parentSelect = document.getElementById('new_category_parent_id');
        if (parentSelect) {
            parentSelect.value = '';
        }
    }

    function appendCategoryOption(select, id, label, path, selected) {
        if (!select) return;
        const option = new Option(label, id, selected, selected);
        if (path) {
            option.title = path;
        }
        select.add(option);
    }

    async function saveQuickCategory() {
        const name = document.getElementById('new_category_name').value.trim();
        const parentId = document.getElementById('new_category_parent_id')?.value || null;

        if (!name) {
            alert('لطفا نام دسته‌بندی را وارد کنید.');
            return;
        }

        try {
            const response = await fetch("{{ route('admin.categories.store-quick') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: name,
                    parent_id: parentId || null,
                }),
            });

            const data = await response.json();

            if (data.success) {
                const label = data.label || data.category.name;
                const path = data.path || label;
                const select = document.getElementById('category_id');
                appendCategoryOption(select, data.category.id, label, path, true);

                if (data.can_have_children) {
                    appendCategoryOption(
                        document.getElementById('new_category_parent_id'),
                        data.category.id,
                        label,
                        path,
                        false
                    );
                }

                closeCategoryModal();
            } else {
                alert(data.message || 'خطایی رخ داده است.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('خطا در برقراری ارتباط با سرور.');
        }
    }
</script>
