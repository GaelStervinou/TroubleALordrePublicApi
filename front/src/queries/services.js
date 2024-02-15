import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getService = async (id) => {
    try {
        const response = await http.get(`/services/${id}`);
        return response.data;
    } catch (error) {
        return null;
    }
}

export const getServices = async (companySlug) => {
    try {
        const response = await http.get(`/companies/${companySlug}/services`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const createService = async (service) => {
    try {
        const response = await http.post(`/services`, service);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const updateService = async (service) => {
    try {
        const response = await http.patch(`/services/${service.id}`, service, {
            headers: {
                "Content-Type": "application/merge-patch+json",
            },
        });
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}