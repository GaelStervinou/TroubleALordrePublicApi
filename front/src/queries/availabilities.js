import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getAvailabilities = async (serviceId, date) => {
    try {
        const response = await http.get(`/availabilities`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getAvailability = async (id) => {
    try {
        const response = await http.get(`/availabilities/${id}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const createAvailability = async (availability) => {
    try {
        const response = await http.post('/availabilities', availability);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const updateAvailability = async (id, availability) => {
    try {
        const response = await http.patch(`/availabilities/${id}`, availability);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const deleteAvailability = async (id) => {
    try {
        await http.delete(`/availabilities/${id}`);
    } catch (error) {
        window.location.href = '/error';
    }
}


