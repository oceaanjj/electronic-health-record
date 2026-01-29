import Swal from 'sweetalert2';

/**
 * SweettttAlerts <33
 * 
 */
const theme = {
	yellow: '#F4CE14',
	darkBrown: '#2A1C0F',
	black: '#000000',
	beige: '#F5F5DC',
	white: '#FFFFFF',
	gray: '#999999'
};

export const showSuccess = (message, title = 'Success!', timer = 3000) => {
	return Swal.fire({
		icon: 'success',
		title: title,
		text: message,
		confirmButtonColor: theme.darkBrown,
		confirmButtonText: 'OK',
		timer: 3000,
		timerProgressBar: true,
		background: theme.white,
		color: theme.black,
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title',
			confirmButton: 'swal-ehr-button'
		}
	});
};

export const showError = (message, title = 'Error!') => {
	return Swal.fire({
		icon: 'error',
		title: title,
		text: message,
		confirmButtonColor: theme.darkBrown,
		confirmButtonText: 'OK',
		background: theme.white,
		color: theme.black,
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title',
			confirmButton: 'swal-ehr-button'
		}
	});
};

export const showWarning = (message, title = 'Warning!') => {
	return Swal.fire({
		icon: 'warning',
		title: title,
		text: message,
		confirmButtonColor: theme.darkBrown,
		confirmButtonText: 'OK',
		background: theme.white,
		color: theme.black,
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title',
			confirmButton: 'swal-ehr-button'
		}
	});
};

export const showInfo = (message, title = 'Info') => {
	return Swal.fire({
		icon: 'info',
		title: title,
		text: message,
		confirmButtonColor: theme.darkBrown,
		confirmButtonText: 'OK',
		background: theme.white,
		color: theme.black,
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title',
			confirmButton: 'swal-ehr-button'
		}
	});
};

export const showConfirm = (message, title = 'Are you sure?', confirmText = 'Yes', cancelText = 'No') => {
	return Swal.fire({
		icon: 'question',
		title: title,
		text: message,
		showCancelButton: true,
		confirmButtonColor: theme.darkBrown,
		cancelButtonColor: theme.gray,
		confirmButtonText: confirmText,
		cancelButtonText: cancelText,
		background: theme.white,
		color: theme.black,
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title',
			confirmButton: 'swal-ehr-button',
			cancelButton: 'swal-ehr-cancel-button'
		}
	});
};

export const showDeleteConfirm = (itemName = 'this item') => {
	return Swal.fire({
		icon: 'warning',
		title: 'Delete Confirmation',
		text: `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: theme.gray,
		confirmButtonText: 'Yes, delete it!',
		cancelButtonText: 'Cancel',
		background: theme.white,
		color: theme.black,
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title',
			confirmButton: 'swal-ehr-button',
			cancelButton: 'swal-ehr-cancel-button'
		}
	});
};

export const showLoading = (title = 'Loading...', text = 'Please wait') => {
	Swal.fire({
		title: title,
		text: text,
		allowOutsideClick: false,
		allowEscapeKey: false,
		showConfirmButton: false,
		background: theme.white,
		color: theme.black,
		didOpen: () => {
			Swal.showLoading();
		},
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title'
		}
	});
};

export const closeAlert = () => {
	Swal.close();
};

export const showLoginRequired = () => {
	return Swal.fire({
		icon: 'warning',
		title: 'Login Required',
		text: 'Please login to continue',
		showCancelButton: true,
		confirmButtonColor: theme.darkBrown,
		cancelButtonColor: theme.gray,
		confirmButtonText: 'Go to Login',
		cancelButtonText: 'Cancel',
		background: theme.white,
		color: theme.black,
		customClass: {
			popup: 'swal-ehr-popup',
			title: 'swal-ehr-title',
			confirmButton: 'swal-ehr-button',
			cancelButton: 'swal-ehr-cancel-button'
		}
	});
};
