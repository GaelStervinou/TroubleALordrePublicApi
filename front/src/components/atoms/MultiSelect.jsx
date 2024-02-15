import Select from "react-select";
import makeAnimated from 'react-select/animated';

const animatedComponents = makeAnimated();

export default function MultiSelect(props) {
    //TODO s'occuper des propTypes ( par ex pr selectOptions vérifier que y'a bien dedans value et label)
    const {selectOptions} = props;
    const defaultValues = props || [];
    const {handleValueChange} = props;
    const {name} = props;
    const {placeholder} = props;

    return (
        <>
            <label htmlFor={name}>{placeholder}</label>
            <Select
                closeMenuOnSelect={false}
                components={animatedComponents}
                defaultValue={defaultValues}
                isMulti
                options={selectOptions}
                onChange={handleValueChange}
            />
        </>
    )
}