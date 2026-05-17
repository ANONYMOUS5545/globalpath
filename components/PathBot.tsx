"use client";

import { useState } from "react";
import { MessageCircle, Send, X } from "lucide-react";

const whatsappUrl = `https://wa.me/${process.env.NEXT_PUBLIC_WHATSAPP_NUMBER ?? "254792579974"}`;

const replies: Record<string, string> = {
  scholarships:
    "Use Scholarships to filter by country, level, coverage and access tier. Expired deadlines are hidden automatically, and each detail page links to the official source.",
  jobs:
    "Use Jobs for direct listings and Job Resources for trusted portals such as UN Careers, World Bank, EU Careers, We Work Remotely and healthcare employer portals.",
  visa:
    "Visa support starts with document order: passport, admission or job evidence, financial proof, accommodation plan and country-specific forms.",
  premium:
    "Premium unlocks higher-value scholarships, premium job tracks and document-supported application tracking while free access remains useful."
};

export function PathBot() {
  const [open, setOpen] = useState(false);
  const [messages, setMessages] = useState([
    {
      role: "bot",
      text: "Hi, I am PathBot. Ask about scholarships, jobs, visas or premium support."
    }
  ]);
  const [input, setInput] = useState("");

  function answer(value: string) {
    const text = value.trim();
    if (!text) return;
    const key = Object.keys(replies).find((item) => text.toLowerCase().includes(item));
    const reply =
      key ? replies[key] : "I can help you find the right page. For personal review, use WhatsApp support so the team can check your documents and timeline.";
    setMessages((items) => [...items, { role: "user", text }, { role: "bot", text: reply }]);
    setInput("");
  }

  return (
    <div className="fixed bottom-5 right-5 z-40 w-[min(360px,calc(100vw-24px))]">
      {open ? (
        <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xl">
          <div className="flex items-center justify-between bg-navy px-4 py-3 text-white">
            <div>
              <div className="font-heading text-sm font-extrabold">PathBot</div>
              <div className="text-xs text-white/65">Scholarships, jobs and visa guidance</div>
            </div>
            <button type="button" onClick={() => setOpen(false)} className="rounded-md p-1 text-white/70 hover:bg-white/10 hover:text-white" aria-label="Close PathBot">
              <X size={18} />
            </button>
          </div>
          <div className="max-h-80 space-y-3 overflow-y-auto bg-slate-50 p-4">
            {messages.map((message, index) => (
              <div
                key={`${message.role}-${index}`}
                className={`rounded-md px-3 py-2 text-sm leading-6 ${
                  message.role === "bot" ? "mr-8 bg-white text-slate-700" : "ml-8 bg-navy text-white"
                }`}
              >
                {message.text}
              </div>
            ))}
          </div>
          <div className="flex flex-wrap gap-2 border-t border-slate-100 p-3">
            {["scholarships", "jobs", "visa", "premium"].map((item) => (
              <button key={item} type="button" onClick={() => answer(item)} className="rounded-md border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-600 hover:border-navy hover:text-navy">
                {item}
              </button>
            ))}
          </div>
          <form
            className="flex gap-2 border-t border-slate-100 p-3"
            onSubmit={(event) => {
              event.preventDefault();
              answer(input);
            }}
          >
            <input value={input} onChange={(event) => setInput(event.target.value)} className="form-input min-w-0 flex-1 py-2 text-sm" placeholder="Ask a quick question" />
            <button type="submit" className="grid h-10 w-10 place-items-center rounded-md bg-gold text-navy" aria-label="Send PathBot message">
              <Send size={17} />
            </button>
          </form>
          <a href={`${whatsappUrl}?text=Hello%20Global%20Path%20Africa%2C%20I%20need%20support`} className="block bg-[#25D366] px-4 py-3 text-center text-sm font-bold text-white">
            Continue on WhatsApp
          </a>
        </div>
      ) : (
        <button type="button" onClick={() => setOpen(true)} className="ml-auto flex h-12 items-center gap-2 rounded-full bg-navy px-4 text-sm font-bold text-white shadow-xl" aria-label="Open PathBot">
          <MessageCircle size={18} />
          PathBot
        </button>
      )}
    </div>
  );
}
