export default function CheckboxInput(props) {
    const {placeholder} = props;
    const {name} = props;
    const value = props.value || "";
    const {disabled} = props || false;
    const {checked} = props || false;

    const {handleValueChange} = props;
    return (
        <div className="flex flex-row gap-2">
            <input
                name={name}
                className="checkbox checkbox-primary"
                type="checkbox"
                placeholder={placeholder}
                value={value}
                onChange={handleValueChange}
                disabled={disabled}
                checked={checked}
            />
            <span className="font-light text-text">{placeholder}</span>
        </div>
    );
}