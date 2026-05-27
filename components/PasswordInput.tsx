"use client";

import { Eye, EyeOff } from "lucide-react";
import { useId, useState } from "react";

type PasswordInputProps = {
  name: string;
  label: string;
  autoComplete: string;
  minLength?: number;
};

export function PasswordInput({ name, label, autoComplete, minLength }: PasswordInputProps) {
  const [visible, setVisible] = useState(false);
  const inputId = useId();

  return (
    <label className="block">
      <span className="mb-1.5 block text-sm font-bold text-slate-700">{label}</span>
      <span className="relative block">
        <input
          id={inputId}
          name={name}
          type={visible ? "text" : "password"}
          required
          minLength={minLength}
          className="form-input pr-12"
          autoComplete={autoComplete}
        />
        <button
          type="button"
          aria-label={visible ? "Hide password" : "Show password"}
          onClick={() => setVisible((current) => !current)}
          className="absolute right-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-navy"
        >
          {visible ? <EyeOff size={18} /> : <Eye size={18} />}
        </button>
      </span>
    </label>
  );
}
