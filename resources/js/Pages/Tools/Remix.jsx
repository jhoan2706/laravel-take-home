import React, { useMemo, useState } from "react";
import axios from "axios";
import { Head } from "@inertiajs/react";

export default function Remix() {
    const MAX_CHARS = 280;

    const [text, setText] = useState("");
    const [variants, setVariants] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");

    const remaining = useMemo(() => MAX_CHARS - text.length, [text]);

    async function onRemix() {
        setError("");
        setVariants([]);
        setLoading(true);

        try {
            const res = await axios.post("/api/remix", { text });
            setVariants(res.data?.variants ?? []);
        } catch (e) {
            const message =
                e?.response?.data?.message ||
                e?.message ||
                "Something went wrong.";
            setError(message);
        } finally {
            setLoading(false);
        }
    }

    async function copyToClipboard(value) {
        try {
            await navigator.clipboard.writeText(value);
        } catch {
            // no-op
        }
    }

    const canSubmit =
        !loading && text.trim().length >= 20 && text.length <= MAX_CHARS;

    return (
        <div className="min-h-screen bg-gray-100 py-12">
            <Head title="Remix a post" />

            <div className="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <h1 className="text-2xl font-bold text-gray-900">
                        Remix a post
                    </h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Paste a draft post and generate 4 alternatives.
                    </p>

                    <div className="mt-8">
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Post text
                        </label>
                        <textarea
                            className="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-4 min-h-[160px]"
                            value={text}
                            onChange={(e) => setText(e.target.value)}
                            placeholder="e.g. 3 reasons your reach is stalled..."
                        />
                        <div className="flex justify-between text-xs text-gray-500 mt-2">
                            <span>Min 20 characters</span>
                            <span
                                className={
                                    remaining < 0
                                        ? "text-red-500 font-bold"
                                        : ""
                                }
                            >
                                {remaining} characters remaining
                            </span>
                        </div>

                        {/* Client-side validation messages */}
                        {text.length > MAX_CHARS ? (
                            <div className="mt-4 p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-600">
                                Text must be 280 characters or fewer.
                            </div>
                        ) : null}

                        {error ? (
                            <div className="mt-4 p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-600">
                                {error}
                            </div>
                        ) : null}

                        <button
                            className={`mt-6 px-6 py-3 rounded-lg font-medium text-white transition-colors ${
                                canSubmit
                                    ? "bg-indigo-600 hover:bg-indigo-700"
                                    : "bg-gray-400 cursor-not-allowed"
                            }`}
                            onClick={onRemix}
                            disabled={!canSubmit}
                        >
                            {loading ? "Remixing..." : "Remix"}
                        </button>
                    </div>

                    <div className="mt-12 border-t pt-8">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">
                            Results
                        </h2>
                        {variants?.length ? (
                            <div className="grid gap-4">
                                {variants.map((v, i) => (
                                    <div
                                        key={i}
                                        className="bg-gray-50 border border-gray-200 rounded-xl p-5 relative group"
                                    >
                                        <div className="text-gray-800 leading-relaxed whitespace-pre-wrap break-words break-all">
                                            {v}
                                        </div>
                                        <button
                                            className="mt-4 text-xs font-medium text-indigo-600 hover:text-indigo-800 underline"
                                            onClick={() => copyToClipboard(v)}
                                        >
                                            Copy to clipboard
                                        </button>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl text-gray-400">
                                Your remixed variants will appear here.
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
