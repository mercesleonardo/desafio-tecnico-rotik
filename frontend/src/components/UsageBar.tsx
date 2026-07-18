export function UsageBar({ percent }: { percent: number }) {
  const clamped = Math.min(percent, 100)
  const color = percent >= 100 ? 'bg-red-600' : percent >= 80 ? 'bg-amber-500' : 'bg-emerald-600'

  return (
    <div
      role="progressbar"
      aria-valuenow={clamped}
      aria-valuemin={0}
      aria-valuemax={100}
      aria-label={`${percent}% do limite de execuções usado`}
      className="h-2 w-full overflow-hidden rounded-full bg-gray-200"
    >
      <div className={`h-full ${color}`} style={{ width: `${clamped}%` }} />
    </div>
  )
}
