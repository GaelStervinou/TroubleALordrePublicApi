import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import CheckboxInput from "../components/atoms/CheckboxInput.jsx";
import { useAuth } from "../app/authContext.jsx";
import { useParams } from "react-router-dom";
import SetUpInstance from "../utils/axios.js";

export default function BackOfficeUsersUpdate() {
  const [roles, setRoles] = useState([]);

  const { userId } = useParams();
  const { isLoggedIn, isAdmin, retrieveUser } = useAuth();
  const navigate = useNavigate();
  const http = SetUpInstance();

  const roleList = [
    'ROLE_ADMIN',
    'ROLE_COMPANY_ADMIN',
    'ROLE_TROUBLE_MAKER'
  ];

  useEffect(() => {
    if (!isLoggedIn() && !isAdmin()) {
      navigate('/');
    } 
  }, []);

  useEffect(() => {
    const fetchUser = async () => {
      const response = await http.get(`/users/${userId}`);
      setRoles(response.data.roles);
    };
    fetchUser();
  }, []);

  const handleRoleChange = (event) => {
    const role = event.target.value;

    if (roles.includes(role)) {
      setRoles(roles.filter((r) => r !== role));
    }
    else {
      setRoles([...roles, role]);
    }
  }

  const handleSubmit = async () => {
    const response = await http.patch(`/users/${userId}`, {roles: roles}, {
      headers: {'Content-Type': 'application/merge-patch+json'}
    });

    if (response.status === 200) {
      navigate('/admin/users');
    }
  }

  return (
    <div className="mt-32 bg-background max-sm:mt-28 w-1/2">
      <div className="hero-content flex-col w-full lg:flex-row-reverse">
        { retrieveUser().id === userId && (
          <WarningAlert message="Attention, vous êtes en train de modifier vos propres rôles.
            Si vous vous retirez le rôle d'administrateur, vous ne pourrez plus accéder à cette page.">
          </WarningAlert>
        )}
          <div className="card shrink-0 max-w-sm w-full shadow-2xl bg-surface">
              <div className="card-body">
                {roleList.map((role, index) => (
                  <CheckboxInput
                    key={index}
                    placeholder={
                      role === 'ROLE_COMPANY_ADMIN' ? 'PDG' :
                      role === 'ROLE_ADMIN' ? 'Administrateur' :
                      role === 'ROLE_TROUBLE_MAKER' ? 'Prestataire' : null
                    }
                    value={role}
                    checked={roles.includes(role)}
                    handleValueChange={handleRoleChange}
                    />
                  ))}
                <Button 
                      title="Mettre à jour"
                      onClick={handleSubmit}
                      hasBackground 
                      className={'!w-full !bg-primary text-background hover:!bg-secondary mt-5'}/>
              </div>
          </div>
      </div>
    </div>
  );

}