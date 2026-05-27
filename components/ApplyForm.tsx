import { FileText, UploadCloud } from "lucide-react";
import { Button } from "./ui/Button";
import type { ApplicationType } from "@/lib/types";

type ApplyFormProps = {
  type: ApplicationType;
  referenceId: string;
  disabled?: boolean;
  disabledLabel?: string;
};

export function ApplyForm({ type, referenceId, disabled, disabledLabel }: ApplyFormProps) {
  if (disabled) {
    return (
      <div className="rounded-md border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-600">
        {disabledLabel ?? "This application is not available."}
      </div>
    );
  }

  return (
    <form action="/api/applications" method="post" encType="multipart/form-data" className="space-y-4">
      <input type="hidden" name="type" value={type} />
      <input type="hidden" name="referenceId" value={referenceId} />
      <label className="block">
        <span className="mb-1.5 block text-sm font-bold text-slate-700">Application note</span>
        <textarea name="notes" className="form-input min-h-24 resize-y" placeholder="Briefly note your target intake, programme or role context." />
      </label>
      <div className="grid gap-3">
        {[
          ["cv", "CV or resume"],
          ["passport", "Passport bio page"],
          ["certificates", "Certificates or transcripts"],
          ["recommendation", "Recommendation letter"]
        ].map(([name, label]) => (
          <label key={name} className="grid cursor-pointer gap-2 rounded-md border border-dashed border-slate-300 bg-white p-3 text-sm text-slate-600 hover:border-navy sm:grid-cols-[18px_minmax(0,1fr)] sm:items-center">
            <UploadCloud size={18} className="text-navy" />
            <span className="min-w-0 font-semibold">{label}</span>
            <input name={name} type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" className="min-w-0 text-xs sm:col-span-2" />
          </label>
        ))}
      </div>
      <Button type="submit" className="w-full">
        <FileText size={16} /> Submit Application
      </Button>
    </form>
  );
}
