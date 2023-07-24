<template>
    <App>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">

                    <Transition name="slide-fade">
                        <div v-if="showAlertSuccess" class="alert alert-success alert-dismissible" role="alert">
                            <strong>Upload Success!</strong> <b>{{ total_rows }}</b> records have been uploaded, and
                            <b>{{ success_rows }}</b> new records have been stored.
                        </div>
                    </Transition>

                    <Transition name="slide-fade">
                        <div v-if="showAlertFailed" class="alert alert-danger alert-dismissible" role="alert">
                            <strong>Failed!</strong> Something wrong please try again.
                        </div>
                    </Transition>

                    <Transition name="slide-fade">
                        <div v-if="showAlertWarning" class="alert alert-warning alert-dismissible" role="alert">
                            <strong>Alert!</strong> Please attach file before upload.
                        </div>
                    </Transition>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="fw-bold"><b>Upload from file</b></h5>
                            <p>
                                <a href="#" color="black" @click="downloadFile">Download Excel Template</a>
                            </p>
                            <div class="input-group">
                                <input type="file" class="form-control d-none" id="fileInput" accept=".xls, .xlsx"
                                    @change="onFileChange">
                                <label class="form-control label-file" for="fileInput" id="fileInputLabel">Choose
                                    file</label>
                                <label class="input-group-text" for="fileInput">Browse</label>
                            </div>

                            <div class="d-flex justify-content-center gap-2 py-4">
                                <button type="submit" class="btn btn-outline-primary d-block w-100 me-2"
                                    @click="uploadFileInput()">
                                    UPLOAD
                                </button>
                                <button type="button" class="btn btn-outline-danger d-block w-100 ms-2"
                                    @click="resetFileInput()">
                                    CANCEL
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table">
                                <thead class="border-top">
                                    <tr>
                                        <th>Name</th>
                                        <th>Level</th>
                                        <th>Class</th>
                                        <th>Parents Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="data in dataModel.students"
                                        v-if="dataModel.students.length > 0 ? hasData = true : hasData = false">
                                        <td>{{ data.name }}</td>
                                        <td>{{ data.level }}</td>
                                        <td>{{ data.classroom }}</td>
                                        <td>{{ data.parent_contact }}</td>
                                    </tr>
                                    <tr class="text-center" v-if="hasData == false">
                                        <td colspan="8">No Data Available</td>
                                    </tr>
                                </tbody>
                            </table>
                            <ul class="pagination mt-4 mb-0 float-end">
                                <li :class="`page-item page-prev ${dataModel.current_page == 1 ? 'disabled' : ''}`">
                                    <a class="page-link" href="javascript:void(0)"
                                        @click.prevent="dataModel.current_page == 1 ? false : dataModel.setPage(dataModel.current_page - 1)">Prev</a>
                                </li>
                                <li :class="`page-item ${dataModel.current_page == page ? 'active' : ''}`"
                                    v-for="page in dataModel.pages">
                                    <a class="page-link" href="javascript:void(0)"
                                        @click.prevent="dataModel.current_page == page ? false : dataModel.setPage(page)">
                                        {{ page }}
                                    </a>
                                </li>
                                <li class="page-item page-next">
                                    <a class="page-link" href="javascript:void(0)"
                                        @click.prevent="dataModel.current_page == dataModel.last_page ? false : dataModel.setPage(dataModel.current_page + 1)">Next</a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </App>
</template>

<script setup lang="ts">
import App from "@/components/App.vue"
import { ref, onMounted } from 'vue';
import { useStudentStore } from "@/stores/uploadFile";
import axios from 'axios';

const hasData = ref(false)
const selectedFile = ref(null);
const dataModel = useStudentStore()
const showAlertSuccess = ref(false)
const showAlertFailed = ref(false)
const showAlertWarning = ref(false)
const total_rows = ref()
const success_rows = ref()

// Trigger change event in input file
const onFileChange = (event) => {
    const file = event.target.files;
    const fileInputLabel = document.getElementById('fileInputLabel');

    // If file exist
    if (file.length > 0) {
        // Change placeholde input to file name
        fileInputLabel.textContent = event.target.files[0].name;

        // Set file excel want to upload
        selectedFile.value = event.target.files[0];
    } else {
        fileInputLabel.textContent = 'Choose file'
    }

}

// Upload file excel
const uploadFileInput = async () => {

    if (selectedFile.value == null) {
        showAlertWarning.value = true
        timerAlert();
        return;
    }

    let formData = new FormData();
    formData.append("file", selectedFile.value);

    // Upload and manage using stores
    const storeData = await dataModel.upload(formData)

    // Result from stores
    switch (storeData) {
        case true:
            // Set counter value
            total_rows.value = dataModel.total_rows
            success_rows.value = dataModel.success_rows

            // Set alert to active
            showAlertSuccess.value = true
            // Reset input and data file
            resetFileInput();
            // Run timer for alert 
            timerAlert();
            break;

        case false:
            // Set alert to active
            showAlertFailed.value = true
            // Reset input and data file
            resetFileInput();
            // Run timer for alert 
            timerAlert();
            break;
        default:
            break;
    }
}

// Timer for Alert auto close
const timerAlert = () => {
    setTimeout(() => {
        if (showAlertSuccess.value == true) {
            // Close alert
            showAlertSuccess.value = false

            // Reset counter
            total_rows.value = 0
            success_rows.value = 0
        } else if (showAlertFailed.value == true) {
            // Close alert
            showAlertFailed.value = false

            // Reset counter
            total_rows.value = 0
            success_rows.value = 0
        } else if (showAlertWarning.value == true) {
            // Close alert
            showAlertWarning.value = false
        }
    }, 7000); // timer 7 second auto close alert
}

// Reset input file
const resetFileInput = () => {
    const fileInput = document.getElementById('fileInput') as HTMLInputElement;
    const fileInputLabel = document.getElementById('fileInputLabel');

    if (fileInput) {
        // Reset data value
        fileInput.value = '';
        selectedFile.value = null;
        fileInputLabel.textContent = 'Choose file'
    }
};

// Download file template student
const downloadFile = async () => {
    try {
        const response = await axios.get('download-file', {
            responseType: 'blob',
        });

        // Create a Blob from the response data
        const blob = new Blob([response.data], { type: 'application/xlsx' });

        // Use the download attribute to create a download link
        const downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = 'template-students.xlsx'; // Replace with the desired file name and extension

        // Programmatically click the download link to initiate the download
        downloadLink.click();

        // Clean up the temporary URL and remove the download link
        URL.revokeObjectURL(downloadLink.href);
    } catch (error) {
        console.error('Error downloading file:', error);
    }
};

onMounted(async () => {
    dataModel.index();
})
</script>

<style>
.label-file {
    margin-left: -2px !important;
    border-top-left-radius: 13px !important;
    border-bottom-left-radius: 13px !important;
    cursor: pointer;
}

.input-group-text {
    background-color: #6b7280;
    color: #ffffff;
    cursor: pointer;
    border-top-right-radius: 13px !important;
    border-bottom-right-radius: 13px !important;
}

a {
    color: black;
}

button {
    border-radius: 13px !important;
}

.slide-fade-enter-active {
    transition: opacity 0.2s ease-out, transform 0.2s ease-out;
    transform-origin: top;
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateY(-100%);
}

.slide-fade-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.slide-fade-leave-active {
    transition: opacity 0.2s ease-out, transform 0.2s ease-out;
    transform-origin: bottom;
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-100%);
}

.slide-fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>