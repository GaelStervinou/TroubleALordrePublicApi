import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import FileInput from "../components/atoms/FileInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import SetUpInstance from '../utils/axios.js';
import { API_MEDIA_ROUTE, API_USERS_ROUTE } from "../utils/apiRoutes.js";

export default function UserUpdate() {
    const [firstname, setFirstname] = useState("");
    const [lastname, setLastname] = useState("");
    const [email, setEmail] = useState("");
    const [profileImage, setProfileImage] = useState(null);
    const [kbis, setKbis] = useState("");
    const [areInvalidCredentials, setAreInvalidCredentials] = useState(false);
    const [buttonText, setButtonText] = useState("Devenir Troublemaker");
    const [wantToBeTroublemaker, setWantToBeTroublemaker] = useState(false);

    const { user, isMyProfile, isTroubleMaker, getMe } = useAuth();
    const navigate = useNavigate();
    const { userId } = useParams();
    const http = SetUpInstance();

    useEffect(() => {
        const checkIsMyProfile = async () => {
            try {
                if (!isMyProfile(userId)) {
                    navigate('/');
                }
                setFirstname(user.firstname);
                setLastname(user.lastname);
                setEmail(user.email);
                if (user.kbis) {
                    setKbis(user.kbis)
                }
            } catch (error) {
                navigate('/login');
            }
        }

        checkIsMyProfile();
    }, [user]);

    useEffect(() => {
        const refreshGetMe = async () => {
            try {
                await getMe();
            } catch (error) {
                // do nothing
            }
        }

        refreshGetMe();
    }, []);

    const handleFirstnameChange = (event) => {
        setFirstname(event.target.value);
    }

    const handleLastnameChange = (event) => {
        setLastname(event.target.value);
    }

    const handleKbisChange = (event) => {
        setKbis(event.target.value);
    }

    const handlePictureChange = (event) => {
        setProfileImage(event.target.files[0])
    }

    const handleTroublemakerChange = () => {
        setWantToBeTroublemaker(!wantToBeTroublemaker);
        if (wantToBeTroublemaker) {
            setButtonText("Devenir Troublemaker");
        } else {
            setButtonText("Ne plus être Troublemaker");
        }
    }

    const handleSubmit = async (event) => {
        event.preventDefault();
        try {
            if (!firstname || !lastname) {
                throw new Error("Invalid credentials");
            }

            var profilePictureId = user.picture;

            if (profileImage !== null) {
                const formData = new FormData();
                formData.append("file", profileImage);
        
                const response = await http.post(API_MEDIA_ROUTE, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                profilePictureId = response.data.id ?? user.picture;
            }

            const role = user.roles;

            if (wantToBeTroublemaker) {
                role.push("ROLE_TROUBLE_MAKER");
            } 

            const userCredentials = {
                firstname: firstname,
                lastname: lastname,
                picture: profilePictureId,
                kbis: kbis,
                roles: role,
            };
            
            await http.patch(API_USERS_ROUTE + `/${user.id}`, userCredentials, {
                headers: {
                    "Content-Type": "application/merge-patch+json",
                },
            });

            navigate('/profile/'+ user.id);
        } catch (error) {
            setAreInvalidCredentials(true);
        }
    }


    return (
        <div className="mt-36 bg-background max-sm:mt-28">
            <div className="hero-content flex-col lg:flex-row-reverse">
                <div className="card shrink-0 w-full max-w-sm shadow-2xl bg-surface">
                    <form className="card-body" onSubmit={handleSubmit}>
                        <div className="flex flex-row gap-5">
                            <TextInput
                                type="text"
                                placeholder="Prénom"
                                value={firstname}
                                handleValueChange={handleFirstnameChange}
                            />
                            <TextInput
                                type="text"
                                placeholder="Nom"
                                value={lastname}
                                handleValueChange={handleLastnameChange}
                            />
                        </div>
                        <FileInput
                            placeholder="Photo de profil"
                            handleValueChange={ handlePictureChange }
                            name={ profileImage }
                            accept="image/*"
                        />
                        <TextInput
                            type="text"
                            placeholder="KBIS"
                            value={kbis}
                            handleValueChange={handleKbisChange}
                        />
                        <TextInput
                            type="email"
                            placeholder="Email"
                            value={email}
                            disabled
                        />
                        { !isTroubleMaker() && (
                            <Button title={buttonText} onClick={handleTroublemakerChange} hasBackground className={'mt-10 !w-full !bg-accent-800 text-background hover:!bg-accent-500'}/>
                        )}

                        <Button type={'submit'} title="Mettre à jour mon profil" hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                        {areInvalidCredentials && (
                            <WarningAlert message="Invalid credentials" />
                        )}
                    </form>
                </div>
            </div>
        </div>
    );
}
