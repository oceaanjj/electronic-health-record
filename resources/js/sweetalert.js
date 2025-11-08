import Swal from 'sweetalert2';
const theme = {
	yellow: '#F4CE14',
	darkBrown: '#2A1C0F',
	black: '#000000',
	beige: '#F5F5DC',
	white: '#FFFFFF'
};

export const showSuccess = (message, title = 'Success!') => {
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

export const showConfirm = (message, title = 'Are you sure?') => {
	return Swal.fire({
		icon: 'question',
		title: title,
		text: message,
		showCancelButton: true,
		confirmButtonColor: theme.darkBrown,
		cancelButtonColor: '#999999',
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
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

export const showLoginRequired = () => {
	return Swal.fire({
		icon: 'warning',
		title: 'Login Required',
		text: 'Please login to add items to cart',
		showCancelButton: true,
		confirmButtonColor: theme.darkBrown,
		cancelButtonColor: '#999999',
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
