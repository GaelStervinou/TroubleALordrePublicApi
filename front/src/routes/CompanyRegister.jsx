import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import FileInput from "../components/atoms/FileInput.jsx";
import MultipleFileInput from "../components/atoms/MultipleFileInput.jsx";
import CheckboxInput from "../components/atoms/CheckboxInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import SetUpInstance from '../utils/axios.js';
import { API_COMPANY_ROUTE } from "../utils/apiRoutes.js";
import { API_MEDIA_ROUTE } from "../utils/apiRoutes.js";

export default function CompanyRegister() {
    const [name, setName] = useState("");
    const [mainImage, setMainImage] = useState(null);
    const [additionalImages, setAdditionalImages] = useState([]);
    const [description, setDescription] = useState("");
    const [areMissingInfos, setAreMissingInfos] = useState(false);
    const [address, setAddress] = useState("");
    const [city, setCity] = useState("");
    const [zipcode, setZipcode] = useState("");
    const [country, setCountry] = useState("");
    const [categories, setCategories] = useState([]);
    const [choosenCategories, setChoosenCategories] = useState([]);
    const [error, setError] = useState(false);

    const { isCompanyAdmin, user, isLoggedIn, getMe } = useAuth();

    const navigate = useNavigate();
    const http = SetUpInstance();

    const handleNameChange = (event) => {
        setName(event.target.value);
    }

    const handleDescriptionChange = (event) => {
        setDescription(event.target.value);
    }

    const handleAddressChange = (event) => {
        setAddress(event.target.value);
    }

    const handleCityChange = (event) => {
        setCity(event.target.value);
    }

    const handleZipcodeChange = (event) => {
        setZipcode(event.target.value);
    }

    const handleCountryChange = (event) => {
        setCountry(event.target.value);
    }

    const handleMainImageChange = (event) => {
        setMainImage(event.target.files[0]);
    }

    const handleSubmit = async (event) => {
        event.preventDefault();
        if (!name || !description || !address || !city || !zipcode || !country || !categories || !mainImage) {
            setAreMissingInfos(true);
            return;
        }

        var mainImageId = null;

        if (mainImage) {
          const formData = new FormData();
          formData.append("file", mainImage);
  
          const response = await http.post(API_MEDIA_ROUTE, formData, {
              headers: {
                  "Content-Type": "multipart/form-data",
              },
          });

          mainImageId = response.data['@id'] ?? null;
        }

        var additionalImagesIds = [];
        if (additionalImages.length > 0) {
          const files = Array.from(additionalImages);

          files.forEach(async (image) => {
            const formData = new FormData();
            formData.append("file", image);
    
            const response = await http.post(API_MEDIA_ROUTE, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            additionalImagesIds.push(response.data['@id']);
          });
        }

        const addressFormated = address.replace(/ /g, '+');
        const response = await fetch('https://api-adresse.data.gouv.fr/search/?q=' + addressFormated + '+' + city + '+' + zipcode + '+' + country + '&limit=1');
        const data = await response.json();
        const longitude = data.features[0].geometry.coordinates[0];
        const latitude = data.features[0].geometry.coordinates[1];

        const companyData = {
            name: name,
            description: description,
            address: address,
            city: city,
            zipCode: zipcode,
            lat: latitude,
            lng: longitude,
            categories: choosenCategories,
            mainMedia: mainImageId,
            medias: additionalImagesIds,
        }

        console.log(companyData);

        try {
          await http.post(API_COMPANY_ROUTE, companyData);
          navigate('/profile/' + user.id);
        } catch (error) {
          setError(true);
        }
    }

    useEffect(() => {
        if (!isLoggedIn() || !isCompanyAdmin()) {
          navigate('/');
        } 
    }, []);

    useEffect(() => {
        const getCategories = async () => {
            const response = await http.get('/categories');
            setCategories(response.data['hydra:member']);
        }
        getCategories();

    }, []);


    return (
      <div className="mt-36 bg-background max-sm:mt-28">
        <div className="hero-content flex-col lg:flex-row-reverse">
            <div className="card shrink-0 w-full max-w-sm shadow-2xl bg-surface">
                <form className="card-body" onSubmit={handleSubmit}>
                    <TextInput
                        placeholder="Nom de l'entreprise"
                        type="text"
                        value={name}
                        handleValueChange={handleNameChange}
                    />
                    <TextInput
                        placeholder="Description"
                        type="text"
                        value={description}
                        handleValueChange={handleDescriptionChange}
                    />
                    <TextInput
                        placeholder="Adresse"
                        type="text"
                        value={address}
                        handleValueChange={handleAddressChange}
                    />
                    <div className="flex flex-row gap-5">
                      <TextInput
                          placeholder="Code postal"
                          type="text"
                          value={zipcode}
                          handleValueChange={handleZipcodeChange}
                      />
                      <TextInput
                          placeholder="Ville"
                          type="text"
                          value={city}
                          handleValueChange={handleCityChange}
                      />
                    </div>
                    <TextInput
                        placeholder="Pays"
                        type="text"
                        value={country}
                        handleValueChange={handleCountryChange}
                    />
                    <label htmlFor="categories" className={'font-medium text-text'}>Catégories</label>
                    { categories.map((category) => {
                        return (
                            <CheckboxInput
                                placeholder={category.name}
                                name={category.id}
                                handleValueChange={(event) => {
                                    if (event.target.checked) {
                                        setChoosenCategories([...choosenCategories, category['@id']]);
                                    } else {
                                        setChoosenCategories(choosenCategories.filter((cat) => cat !== category['@id']));
                                    }
                                }}
                            />
                        )
                    })}
                    <FileInput
                        placeholder="Image principale"
                        handleValueChange={handleMainImageChange}
                        name = { mainImage }
                        accept = "image/*"
                    />
                    <MultipleFileInput
                        placeholder="Images supplémentaires"
                        name = { additionalImages }
                        files = { additionalImages }
                        setFiles = { setAdditionalImages }
                        maxFiles = { 5 }
                        maxFileSize = { 5000000 }
                        accept = "image/*"
                        preview = { true }
                        multiple = { true }
                    />
                    <Button
                        type="submit"
                        title="Créer"
                        hasBackground
                        className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}
                    />
                    {areMissingInfos && <WarningAlert message="Veuillez remplir tous les champs" />}
                    {error && <WarningAlert message="Erreur lors de la création de l'entreprise" />}
                </form>
            </div>
        </div>
      </div>
    );
}
