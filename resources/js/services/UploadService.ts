import axios from 'axios';

export let uploading = false
export let progress = 0
export let message = ''

export const UploadService = {
  uploadFile: async (project_id: string, file: File, csrfToken: string) => {
    uploading = true; // Set uploading state
    progress = 0; // Reset progress
    message = ''; // Clear previous messages

    const formData = new FormData();
    formData.append('file', file); // Append the file to FormData

    try {
      // Make the POST request using Axios
      const response = await axios.post(`/projects/${project_id}/uploadFile`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'X-CSRF-TOKEN': csrfToken, // Include CSRF token
        },
        // Axios progress event handler
        onUploadProgress: (progressEvent) => {
          if (progressEvent.total) {
            progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
          }
        },
      });

      // Handle successful upload
      message = response.data.message; // Display success message from API
    } catch (error) {
      // Handle errors during upload
      if (error.response) {
        // Server responded with an error status code
        message = error.response.data?.message || 'Upload failed. Please try again.';
        if (error.response.status === 422) {
          // Handle validation errors specifically if needed
          console.error('Validation Errors:', error.response.data.errors);
          // You could potentially display specific validation errors here
          message = `Upload failed: ${error.response.data.errors?.file?.[0] || 'Invalid input.'}`;
        }
      } else if (error.request) {
        // Request was made but no response received
        message = 'Upload failed. No response from server.';
      } else {
        // Something else happened setting up the request
        message = 'Upload failed. An unexpected error occurred.';
      }
      console.error('Upload error:', error);
    } finally {
      // Runs regardless of success or failure
      uploading = false; // Reset uploading state
      // Keep progress bar visible briefly after completion/error, then hide
      setTimeout(() => {
        if (!uploading) {
          // Only reset if another upload hasn't started
          progress = 0;
        }
      }, 1500);
    }
  },
};
