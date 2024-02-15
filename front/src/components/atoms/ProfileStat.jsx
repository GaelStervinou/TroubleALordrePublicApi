export default function ProfileStat({title, value}) {
    return (
        <div className={'flex flex-col items-center text-text'}>
            <p className={'font-bold text-3xl max-md:text-xl'}>{value}</p>
            <label className={'text-lg max-sm:text-sm'}>{title}</label>
        </div>
    )
}