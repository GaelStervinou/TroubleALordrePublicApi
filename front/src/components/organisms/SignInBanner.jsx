import {AiFillThunderbolt} from "react-icons/ai";
import Button from "../atoms/Button.jsx";
export default function SignInBanner () {

    return (
        <>
            <section className="w-full my-20 flex flex-col items-center relative max-md:my-6 gap-8">
                <div className="w-2/6 absolute h-60 max-md:h-36">
                    <div style={{boxShadow: 'rgb(156,115,33) 0px 0px 80px 50px'}}
                         className="z-0 absolute top-1/4 left-3/4 rounded-full"></div>
                    <div style={{boxShadow: 'rgb(156,115,33) 0px 0px 100px 80px'}}
                         className="z-0 absolute top-full right-3/4 rounded-full"></div>
                </div>
                <div className="flex flex-col items-center gap-3 my-8 mx-4 z-10 max-md:mx-2 max-md:my-2">
                    <h2 className="text-primary font-medium mt-4 text-center font-heading text-3xl max-md:text-base max-md:mt-0">
                        Vous êtes propriétaire <br/>d'un établissement ?
                    </h2>
                    <p className="text-lg text-center max-md:text-base">
                        Augmentez vos reserverations en ajoutant votre établissement
                    </p>
                </div>
                    <Button
                      type={'submit'}
                      title="Ajoutez votre établisement"
                      href={'/company-register'}
                      icon={<AiFillThunderbolt/>}
                      hasBackground
                      className={'z-10'}/>
            </section>
        </>
    );
}