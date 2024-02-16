import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getCompanyInvitations = async (companySlug) => {
    try {
        const response = await http.get(`/companies/${companySlug}/invitations`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getMyInvitations = async () => {
    try {
        const response = await http.get(`/users/my-invitations`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const createInvitation = async (invitation) => {
    try {
        const response = await http.post(`/invitations`, invitation);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const updateInvitation = async (invitationId, invitationStatus) => {
    try {
        const response = await http.patch(`${invitationId}`, invitationStatus, {
            headers: {
                "Content-Type": "application/merge-patch+json",
            },
        });
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}