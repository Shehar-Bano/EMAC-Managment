/**
 * ERP System - Global JavaScript Utilities & SweetAlert2 Bridge
 * Standardized across all ERP modules according to ERP_STANDARDS.md
 */

window.ERP = {
    // Toast notification
    toast: (icon = 'success', title = '') => {
        if (typeof Swal === 'undefined') {
            alert(title);
            return;
        }
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        Toast.fire({ icon, title });
    },

    showSuccess: (message, title = 'Success!') => {
        if (typeof Swal === 'undefined') {
            alert(message);
            return;
        }
        Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 4000,
            timerProgressBar: true
        });
    },

    showError: (message, title = 'Error Encountered') => {
        if (typeof Swal === 'undefined') {
            alert(message);
            return;
        }
        Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Close'
        });
    },

    showWarning: (message, title = 'Warning') => {
        if (typeof Swal === 'undefined') {
            alert(message);
            return;
        }
        Swal.fire({
            icon: 'warning',
            title: title,
            text: message,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Understood'
        });
    },

    showInfo: (message, title = 'Information') => {
        if (typeof Swal === 'undefined') {
            alert(message);
            return;
        }
        Swal.fire({
            icon: 'info',
            title: title,
            text: message,
            confirmButtonColor: '#0ea5e9',
            confirmButtonText: 'OK'
        });
    },

    /**
     * Standard Delete Confirmation Dialog
     */
    confirmDelete: (target, options = {}) => {
        if (typeof Swal === 'undefined') {
            if (confirm(options.text || 'Are you sure you want to delete this record?')) {
                if (typeof target === 'function') target();
                else if (target && target.submit) target.submit();
            }
            return;
        }

        Swal.fire({
            title: options.title || 'Delete Record?',
            text: options.text || 'This action cannot be undone. Are you sure you wish to proceed?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: options.confirmButtonText || 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof target === 'function') {
                    target();
                } else if (target && target.submit) {
                    target.submit();
                }
            }
        });
    },

    /**
     * Standard Bulk Delete Confirmation Dialog
     */
    confirmBulkDelete: (callback, options = {}) => {
        const count = options.count || 'the selected';
        if (typeof Swal === 'undefined') {
            if (confirm(`Are you sure you want to delete ${count} record(s)?`)) {
                if (typeof callback === 'function') callback();
            }
            return;
        }

        Swal.fire({
            title: options.title || `Delete ${count} Selected Item(s)?`,
            text: options.text || 'All selected items that have no blocking dependencies will be removed. This action cannot be reversed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: options.confirmButtonText || 'Yes, delete selected',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    },

    /**
     * Handle AJAX / Fetch errors gracefully
     */
    handleAjaxError: (xhr) => {
        let message = 'An unexpected error occurred. Please try again.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        } else if (xhr.status === 403) {
            message = 'Access Denied: You do not have permission to perform this action.';
        } else if (xhr.status === 404) {
            message = 'Requested record was not found.';
        } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            const firstKey = Object.keys(xhr.responseJSON.errors)[0];
            message = xhr.responseJSON.errors[firstKey][0];
        }

        ERP.showError(message);
    },

    /**
     * Initialize standard Table Checkboxes & Bulk Action bar
     */
    initTableCheckboxes: () => {
        const selectAllHeader = document.querySelector('.table-select-all');
        const rowCheckboxes = document.querySelectorAll('.table-row-checkbox');
        const bulkActionContainer = document.querySelector('.bulk-action-bar');
        const selectedCountElements = document.querySelectorAll('.selected-count-badge');
        const bulkDeleteBtn = document.querySelector('#bulk-delete-btn');

        if (!selectAllHeader && rowCheckboxes.length === 0) return;

        const updateState = () => {
            const checkedBoxes = document.querySelectorAll('.table-row-checkbox:checked');
            const count = checkedBoxes.length;

            selectedCountElements.forEach(el => {
                el.textContent = count;
            });

            if (bulkActionContainer) {
                if (count > 0) {
                    bulkActionContainer.classList.remove('hidden');
                    bulkActionContainer.classList.add('flex');
                } else {
                    bulkActionContainer.classList.add('hidden');
                    bulkActionContainer.classList.remove('flex');
                }
            }

            if (bulkDeleteBtn) {
                bulkDeleteBtn.disabled = count === 0;
            }

            if (selectAllHeader) {
                selectAllHeader.checked = (count > 0 && count === rowCheckboxes.length);
                selectAllHeader.indeterminate = (count > 0 && count < rowCheckboxes.length);
            }
        };

        if (selectAllHeader) {
            selectAllHeader.addEventListener('change', (e) => {
                rowCheckboxes.forEach(cb => {
                    cb.checked = e.target.checked;
                });
                updateState();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateState);
        });

        updateState();
    }
};

// Global click event handler for dropdowns and delete confirmations
document.addEventListener('DOMContentLoaded', () => {
    ERP.initTableCheckboxes();

    // Delegate delete button forms
    document.addEventListener('click', (e) => {
        const deleteTrigger = e.target.closest('[data-confirm-delete]');
        if (deleteTrigger) {
            e.preventDefault();
            const form = deleteTrigger.closest('form');
            const message = deleteTrigger.getAttribute('data-confirm-delete');
            ERP.confirmDelete(form, { text: message || undefined });
        }
    });
});
