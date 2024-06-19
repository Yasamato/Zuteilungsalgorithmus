"use client"

import { useState } from "react"

export function CollapseButton({
  children,
  initialCollapsed = false,
  className,
  srLabel = "navbar menu",
}: {
  children: React.ReactNode
  initialCollapsed?: boolean
  className?: string
  srLabel: string
}) {
  const [collapsed, setCollapsed] = useState(initialCollapsed)

  return (
    <>
      <button
        type="button"
        className="inline-flex h-10 w-10 items-center justify-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 md:hidden dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
        onClick={() => {
          setCollapsed(!collapsed)
        }}
      >
        <span className="sr-only">
          {collapsed ? "Open" : "Close"} {srLabel}
        </span>
        <svg
          className="h-5 w-5"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 17 14"
        >
          <path
            stroke="currentColor"
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth="2"
            d="M1 1h15M1 7h15M1 13h15"
          />
        </svg>
      </button>
      <div className={(collapsed ? "hidden" : "") + " " + className}>
        {children}
      </div>
    </>
  )
}
