import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getReservations = async () => {
    try {
        const response = await http.get('/reservations');
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getReservation = async (id) => {
    try {
        const response = await http.get(`/reservations/${id}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const createReservation = async (reservation) => {
    try {
        const response = await http.post('/reservations', reservation);
        return response.data;
    } catch (error) {
        return error.response.data;
    }
}

export const updateReservation = async (id, reservation) => {
    try {
        const response = await http.patch(`/reservations/${id}`, reservation);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getUserReservations = async (userId) => {
    try {
        const response = await http.get(`/users/${userId}/reservations`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getTroubleMakerReservations = async (userId) => {
    try {
        const response = await http.get(`/users/${userId}/troublemaker/reservations`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}