export default function FileInput(props) {
    const {placeholder} = props;
    const {name} = props;
    const {handleValueChange} = props;
    const {accept} = props || "*";

    return (
        <div className="flex flex-col gap-2">
            <label htmlFor={name} className={'font-medium text-text'}>{placeholder}</label>
            <input
                type="file"
                name={name}
                accept={accept}
                onChange={handleValueChange}
                className="file-input file-input-bordered w-full max-w-xs"
            />
        </div>
    )
}