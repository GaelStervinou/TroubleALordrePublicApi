import {useEffect, useState} from "react";
import {Line} from "react-chartjs-2";
import {
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip
} from "chart.js";
import {getCompanyDashboard, getCompanyUsers} from "../queries/companies.js";
import {useParams} from "react-router-dom";
import CardRoundedList from "../components/organisms/CardRoundedList.jsx";

export default function CompanyDashboardPage() {
    const {companySlug} = useParams();
    let [companyDashboard, setCompanyDashboard] = useState({});
    let [collaboratorList, setCollaboratorList] = useState([]);
    let [isLoading, setIsLoading] = useState(true);

    ChartJS.register(
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        Title,
        Tooltip,
        Legend
    );

    const options = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
        },
    };


    useEffect(() => {
        const fetchCompanyUsers = async () => {
            const fetchedCompanyUsers = await getCompanyUsers(companySlug);
            setCollaboratorList(fetchedCompanyUsers);
        }
        const fetchCompanyDashboard = async () => {
            const fetchedCompanyDashboard = await getCompanyDashboard(companySlug);
            const labels = Object.keys(fetchedCompanyDashboard.reservationsCurrentMonth).map(date => {
                const [, month, day] = date.split('-');
                return `${day}/${month}`;
            });
            const reservationsCurrentMonth = Object.values(fetchedCompanyDashboard.reservationsCurrentMonth);
            const reservationsPreviousMonth = Object.values(fetchedCompanyDashboard.reservationsPreviousMonth);
            const monthSalesCurrentMonth = Object.values(fetchedCompanyDashboard.monthSalesCurrentMonth);
            const monthSalesPreviousMonth = Object.values(fetchedCompanyDashboard.monthSalesPreviousMonth);

            let previousMonth = 0;

            if (fetchedCompanyDashboard.numberOfReservationsPreviousMonth === 0) {
                previousMonth = 1;
            } else {
                previousMonth = fetchedCompanyDashboard.numberOfReservationsPreviousMonth;
            }

            let numberOfReservationsPeriodeRange = (
                (fetchedCompanyDashboard.numberOfReservationsCurrentMonth - fetchedCompanyDashboard.numberOfReservationsPreviousMonth)
                / previousMonth) * 100;
            numberOfReservationsPeriodeRange = numberOfReservationsPeriodeRange.toFixed(2);

            let bestTroubleMakerRatioOfTotalReservations = (fetchedCompanyDashboard.bestTroubleMaker?.currentMonthTotalReservations / fetchedCompanyDashboard.numberOfReservationsCurrentMonth) * 100;

            setCompanyDashboard({
                "numberOfReservationsCurrentMonth": fetchedCompanyDashboard.numberOfReservationsCurrentMonth,
                "numberOfReservationsPeriodeRange": numberOfReservationsPeriodeRange,
                "averageRateForCurrentMonth" : fetchedCompanyDashboard.averageRateForCurrentMonth.toFixed(1),
                "averageRateForPreviousMonth" : fetchedCompanyDashboard.averageRateForPreviousMonth.toFixed(1),
                "bestTroubleMaker": {
                    "name": `${fetchedCompanyDashboard.bestTroubleMaker?.firstName} ${fetchedCompanyDashboard.bestTroubleMaker?.lastName}`,
                    "contentUrl": fetchedCompanyDashboard.bestTroubleMaker?.profilePicture.contentUrl,
                    "currentMonthTotalReservations": fetchedCompanyDashboard.bestTroubleMaker?.currentMonthTotalReservations,
                    "bestTroubleMakerRatioOfTotalReservations": bestTroubleMakerRatioOfTotalReservations.toFixed(2)
                },
                "reservationsChartData": {
                    labels,
                    datasets: [
                        {
                            label: 'last 30 days',
                            data: reservationsCurrentMonth,
                            borderColor: 'rgb(255, 228, 79)',
                            backgroundColor: 'rgba(255, 228, 79, 0.5)',
                        },
                        {
                            label: 'previous period',
                            data: reservationsPreviousMonth,
                            borderColor: 'rgb(156, 115, 33)',
                            backgroundColor: 'rgba(156, 115, 33, 0.5)',
                        },
                    ],
                },
                "monthSalesChartData": {
                    labels,
                    datasets: [
                        {
                            label: 'last 30 days',
                            data: monthSalesCurrentMonth,
                            borderColor: 'rgb(255, 228, 79)',
                            backgroundColor: 'rgba(255, 228, 79, 0.5)',
                        },
                        {
                            label: 'previous period',
                            data: monthSalesPreviousMonth,
                            borderColor: 'rgb(156, 115, 33)',
                            backgroundColor: 'rgba(156, 115, 33, 0.5)',
                        }
                    ]
                }
            });
            setIsLoading(false);
        };
        fetchCompanyUsers();
        fetchCompanyDashboard();

    }, [companySlug]);


    return (
        <div className={'mt-28 max-sm:pb-40 max-sm:mt-16 w-full px-16 max-sm:p-8 space-y-8'}>

            <div className="stats bg-surface w-full text-text">

                <div className="stat p-4">
                    <div className="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" className="inline-block w-8 h-8 stroke-current"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <div className="stat-title text-text">
                        {isLoading ? (<div className="skeleton w-10 h-4 mb-1"></div>) : ('Note')}
                    </div>
                    <div className="stat-value text-primary">
                        {isLoading ? (<div className="skeleton w-20 h-8 mb-1"></div>) : companyDashboard.averageRateForCurrentMonth}
                    </div>
                    <div className="stat-desc text-text">
                        {isLoading ? (<div className="skeleton w-24 h-3"></div>) : `${companyDashboard.averageRateForPreviousMonth} le mois dernier`}
                    </div>
                </div>

                <div className="stat p-4">
                    <div className="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" className="inline-block w-8 h-8 stroke-current"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div className="stat-title text-text">
                        {isLoading ? (<div className="skeleton w-1/2 h-4 mb-1"></div>) : ('Prestations réalisées')}
                    </div>
                    <div className="stat-value text-secondary">
                        {isLoading ? (<div className="skeleton w-20 h-8 mb-1"></div>) : companyDashboard.numberOfReservationsCurrentMonth}
                    </div>
                    <div className="stat-desc text-text">
                        {isLoading ?
                            (<div className="skeleton w-24 h-3"></div>) :
                            (`${companyDashboard.numberOfReservationsPeriodeRange ?? 0 }% ${companyDashboard.numberOfReservationsPreviousMonth > companyDashboard.numberOfReservationsCurrentMonth ? 'moins' : 'plus' } que le mois dernier`)
                        }
                    </div>
                </div>
                { companyDashboard.bestTroubleMaker?.currentMonthTotalReservations > 0 && (
                    <div className="stat p-4">
                        <div className="stat-figure text-secondary">
                            <div className="avatar">
                                <div className="w-16 rounded-full">
                                    {isLoading ?
                                        (<div className="skeleton w-16 h-16 rounded-full"></div>) :
                                        (<img src={`${import.meta.env.VITE_API_BASE_URL}${companyDashboard.bestTroubleMaker?.contentUrl}`} alt={companyDashboard.bestTroubleMaker?.name} />)
                                    }
                                </div>
                            </div>
                        </div>
                        <div className="stat-value">
                            {isLoading ?
                                (<div className="skeleton w-1/2 h-4"></div>) :
                                (`${companyDashboard.bestTroubleMaker?.bestTroubleMakerRatioOfTotalReservations}%`)
                            }
                        </div>
                        <div className="stat-title text-text">
                            {isLoading ? (<div className="skeleton w-1/2 h-4"></div>) : ('Meilleur collaborateur')}
                        </div>
                        <div className="stat-desc text-secondary">
                            {isLoading ? (<div className="skeleton w-24 h-3"></div>) : (`${companyDashboard.bestTroubleMaker?.currentMonthTotalReservations} prestations réalisées`)}
                        </div>
                    </div>
                )}

            </div>

            <section className={'flex gap-8 max-sm:flex-col'}>
                <div className={'w-1/2 overflow-y-hidden max-sm:w-full bg-surface py-4 rounded-xl max-sm:py-4'}>
                    <h5 className={'stat-title text-text font-medium pl-4'}>
                        {isLoading ? (<div className="skeleton w-1/2 h-4"></div>) : ('Prestations réalisées')}
                    </h5>
                    <div className={"px-4 pt-4"}>
                        {isLoading ?
                            (<div className="skeleton w-full h-72"></div>) :
                            (<Line options={options} data={companyDashboard.reservationsChartData} />)
                        }
                    </div>
                </div>
                <div className={'w-1/2 overflow-y-hidden max-sm:w-full bg-surface py-4 rounded-xl max-sm:py-4'}>
                    <h5 className={'stat-title text-text font-medium pl-4'}>
                        {isLoading ? (<div className="skeleton w-1/2 h-4"></div>) : ('Chiffre d\'affaire')}
                    </h5>
                    <div className={"px-4 pt-4"}>
                        {isLoading ?
                            (<div className="skeleton w-full h-72"></div>) :
                            (<Line options={options} data={companyDashboard.monthSalesChartData} />)
                        }
                    </div>
                </div>
            </section>

            <div className={'bg-surface py-4 rounded-xl max-sm:py-4 space-y-4'}>
                <h5 className={'stat-title text-text font-medium pl-8'}>
                    L'équipe
                </h5>
                <CardRoundedList items={collaboratorList}/>
            </div>
        </div>
    );
}