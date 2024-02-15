import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getCompanies = async () => {
    try {
        const response = await http.get('/companies');
        return response.data['hydra:member'];
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getRandomCompanies = async () => {
    let page = Math.floor(Math.random() * 5) + 1;
    try {
        const response = await http.get(`/companies?page=${page}`);
        return response.data['hydra:member'];
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getCompany = async (id) => {
    try {
        const response = await http.get(`/companies/${id}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const createCompany = async (company) => {
    try {
        const response = await http.post('/companies', company);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const updateCompany = async (id, company) => {
    try {
        const response = await http.patch(`/companies/${id}`, company);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getCompanyServices = async (id, page = 1) => {
    try {
        const response = await http.get(`/companies/${id}/services?page=${page}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getCompanyUsers = async (id) => {
    try {
        const response = await http.get(`/companies/${id}/users`);
        return response.data['hydra:member'];
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getUserCompanies = async (id, page = 1) => {
    try {
        const response = await http.get(`/users/${id}/owned-companies?page=${page}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getCompanyDashboard = async (id) => {
    try {
        const response = await http.get(`/companies/${id}/dashboard`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getSearch = async (lat, lng, categorieId) => {
    try {
        const response = await http.get(`/companies/search?lat=${lat}&lng=${lng}&categories.id=${categorieId}`);
        return response.data['hydra:member'];
    } catch (error) {
        return error;
    }
}