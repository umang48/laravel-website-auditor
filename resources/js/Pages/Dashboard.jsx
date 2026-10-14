import React from 'react';

export default function Dashboard({ websites }) {
    return (
        <div className="p-8 max-w-4xl mx-auto">
            <h1 className="text-3xl font-bold mb-6">Dashboard</h1>
            <div className="bg-white p-6 rounded-lg shadow">
                <p>Welcome to your Website Auditor Dashboard.</p>
                <div className="mt-4">
                    <h2 className="text-xl font-semibold">Your Websites</h2>
                    {websites.length > 0 ? (
                        <ul className="mt-2 space-y-2">
                            {websites.map(site => (
                                <li key={site.id} className="p-4 border rounded">
                                    {site.name} - {site.url}
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="text-gray-500 mt-2">No websites added yet.</p>
                    )}
                </div>
            </div>
        </div>
    );
}