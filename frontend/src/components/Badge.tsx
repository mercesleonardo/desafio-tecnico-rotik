import type { ReactNode } from 'react'

const variants = {
  success: 'bg-emerald-100 text-emerald-800',
  warning: 'bg-amber-100 text-amber-800',
  danger: 'bg-red-100 text-red-800',
  neutral: 'bg-gray-200 text-gray-700',
} as const

interface BadgeProps {
  variant: keyof typeof variants
  children: ReactNode
}

export function Badge({ variant, children }: BadgeProps) {
  return (
    <span className={`rounded-full px-2 py-0.5 text-xs font-medium ${variants[variant]}`}>
      {children}
    </span>
  )
}
